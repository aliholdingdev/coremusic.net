---
title: "00-kspace-anayasa — K000→K5999 K-Space Anayasa Eki (EK A)"
type: rules
category: architecture
version: "1.0.0"
status: active
authority: "SSOT: K-space bant/kimlik kaynak defteri — orijinal F1 v2.2.0 EK A kopyası (G:\…master-prompt-v1.md satır 1552-7392), değiştirilmeden + research kapısı notları eklendikçe güncellenir"
updated: 2026-10-08
tier: 3
domain: architecture
ssot: true
risk: high
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md"]
---

# EK A — K000 → K5999 KATMAN ENVANTERİ (ANAYASA EKİ)

> Bu envanter bölüm 3 Görev 04/05/06 çıktılarının tamamıdır. Her katman
> EK C'deki 20 alanlı kimlik kartına sahiptir (özet satırı burada, tam
> kart gerektiğinde üretilir). Damga = web kanıtı henüz eklenmedi;
> bölüm 7.3 research kapısında doldurulur. Kanonik K-ID asla değişmez,
> teatral ad yalnız epitetir.

```
K000    K021    K121    K501    K1021   K2021   K3021   K4021   K5021   K5999
┌───────┬───────┬───────┬───────┬───────┬───────┬───────┬───────┬───────┐
    1       2       3       4       5       6       7       8       9

1  K000-K020    EXISTING FOUNDATION
2  K021-K120    ENTERPRISE EXPANSION
3  K121-K500    SPECIALIZED DOMAINS
4  K501-K1020   EXTENDED / DEEP PLATFORM
5  K1021-K2020  DEEP DOMAIN / RUNTIME / CROSS-CUT
6  K2021-K3020  HARDWARE / MEDIA / UI DEEP
7  K3021-K4020  AGENT / SIM / RESEARCH / GOV
8  K4021-K5020  COMMERCE / RIGHTS / AI / DATA
9  K5021-K5999  RESILIENCE / FUTURE / RESERVED
```

## A.0 — ANAHTAR TABLOSU (K000 → K020 FOUNDATION)

| K-ID | Kanonik Ad | Teatral Epitet | Uçak |
|---|---|---|---|
| K000 | OS | «TEMEL TAŞI» | SOFTWARE |
| K001 | HARDWARE | «DEMİRHANE» | SOFTWARE |
| K002 | DRIVERS | «KANAT» | SOFTWARE |
| K003 | AUDIO ENGINE | «ÇEKİRDEK» | SOFTWARE |
| K004 | AI | «MIKNATIS» | SOFTWARE |
| K005 | DATA | «FENER» | SOFTWARE |
| K006 | SECURITY | «ÇELİK KAPI» | SOFTWARE |
| K007 | MIDDLEWARE | «SUR» | SOFTWARE |
| K008 | SERVICES | «KULE» | SOFTWARE |
| K009 | API | «HÜCRE» | SOFTWARE |
| K010 | APPLICATION | «DÜĞÜM» | SOFTWARE |
| K011 | UX | «OMURGA» | SOFTWARE |
| K012 | OBSERVABILITY | «AYNA» | SOFTWARE |
| K013 | CI/CD | «PUSULA» | SOFTWARE |
| K014 | NETWORK | «ÇARK» | SOFTWARE |
| K015 | MEDIA | «MÜHÜR» | SOFTWARE |
| K016 | AMPLIFIER | «ZAR» | PHYSICAL |
| K017 | POWER | «KANTAR» | PHYSICAL |
| K018 | THERMAL | «MEZİT» | PHYSICAL |
| K019 | PCB | «ALEV» | PHYSICAL |
| K020 | MANUFACTURING | «BUZUL» | PHYSICAL |

## A.1 — FOUNDATION KATMANLARI (K000 → K020)

### K000 - OS «TEMEL TAŞI» Bant: K000-K020
- Sorumluluk: OS Runtime · Kernel Services · Process/Thread · Filesystem · Memory · Networking · Device Abstraction · Container Runtime
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=— (kök) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K001 - HARDWARE «DEMİRHANE» Bant: K000-K020
- Sorumluluk: Audio Hardware · XMOS XU316 · PCM3168A · AK4458 · Amplifier · Connectors · Power Interfaces · Sensors
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K000 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K002 - DRIVERS «KANAT» Bant: K000-K020
- Sorumluluk: ASIO · WASAPI · ALSA · PipeWire · CoreAudio · I2S · USB Audio · Bluetooth · DLNA
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K001 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K003 - AUDIO ENGINE «ÇEKİRDEK» Bant: K000-K020
- Sorumluluk: Neva Engine · Audio Pipeline · DSP · 31-Band EQ · Filters · Dynamics · Reverb · Limiter · Crossover · Surround
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K002 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K004 - AI «MIKNATIS» Bant: K000-K020
- Sorumluluk: Music Analysis · Recommendation · Voice · Auto EQ · Audio Intelligence · Edge AI · ML Infra · Personalization
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K003 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K005 - DATA «FENER» Bant: K000-K020
- Sorumluluk: MySQL · BCNF Schema · Transactions · Indexes · Redis · APCu · SQLite · Restic Backup
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K004 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K006 - SECURITY «ÇELİK KAPI» Bant: K000-K020
- Sorumluluk: Authentication · Authorization · RBAC · Policy · JWT · CSRF · CSP · Rate Limiting · Vault · Audit
- Sınır: data=çekirdek veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K005 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K007 - MIDDLEWARE «SUR» Bant: K000-K020
- Sorumluluk: Request Pipeline · Origin Check · CORS · Rate Limit · Security Headers · Session · PSR-15
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K006 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K008 - SERVICES «KULE» Bant: K000-K020
- Sorumluluk: Control · Media · Audio · Device · Network · AI · Download · Event Boundary
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K007 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K009 - API «HÜCRE» Bant: K000-K020
- Sorumluluk: API Gateway · BFF · REST API · OpenAPI · CQRS · Event Bus · SPA Router · Error Contract
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K008 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K010 - APPLICATION «DÜĞÜM» Bant: K000-K020
- Sorumluluk: Music App · Home App · Car App · Studio App · Mobile · Web · Admin · User Workflows
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K009 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K011 - UX «OMURGA» Bant: K000-K020
- Sorumluluk: Playback UI · Library UI · Search UI · Queue UI · Equalizer UI · Ambient Aura · Theme · Accessibility
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K010 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K012 - OBSERVABILITY «AYNA» Bant: K000-K020
- Sorumluluk: Structured Logging · Metrics · Tracing · Correlation · Health Checks · Audit Trail · Alerting · Audio Diagnostics
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K011 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K013 - CI/CD «PUSULA» Bant: K000-K020
- Sorumluluk: Source Control · Build · Static Analysis · Unit/Integration/E2E · Security Scanning · Artifacts · Rollback
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K012 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K014 - NETWORK «ÇARK» Bant: K000-K020
- Sorumluluk: TCP/IP · HTTP/S · WebSocket · WebRTC · DNS · TLS · LAN/WAN · Offline Sync · Handoff · Multi-Room
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K013 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K015 - MEDIA «MÜHÜR» Bant: K000-K020
- Sorumluluk: Music Library · Local/Cloud Playback · Offline-First · FLAC/WAV/MP3 · Metadata · Queue · Download · Media Cache
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K014 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K016 - AMPLIFIER «ZAR» Bant: K000-K020
- Sorumluluk: Class AB · Class D · DAC Output · ADC Input · Speaker Out · Subwoofer · Multi-Channel · Gain · Protection
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K015 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K017 - POWER «KANTAR» Bant: K000-K020
- Sorumluluk: AC Input · DC Rails · PSU · Regulation · LM5122 · Sequencing · OVP · OCP · Fault Handling
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K016 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K018 - THERMAL «MEZİT» Bant: K000-K020
- Sorumluluk: Thermal Model · Heat Sources · Heat Sink · Fan Control · Temp Sensors · Protection · Shutdown
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K017 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K019 - PCB «ALEV» Bant: K000-K020
- Sorumluluk: Analog/Digital/Power Section · Clocking · Grounding · Signal Integrity · EMI/EMC · Routing
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K018 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

### K020 - MANUFACTURING «BUZUL» Bant: K000-K020
- Sorumluluk: BOM · Suppliers · Assembly · Calibration · Production Test · QC · Serialisation · Field Service
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K019 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault

## A.2 — ENTERPRISE / PROFESSIONAL KATMANLARI (K021 → K120)

### K021 - DISTRIBUTED SYSTEMS «SUR» Bant: K021-K120
- Sorumluluk: Node Architecture · Cluster Coordination · Failure Domains · Consistency · Partition Handling
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K022 - CLOUD PLATFORM «KULE» Bant: K021-K120
- Sorumluluk: Compute · Storage · Networking · Runtime · Cloud Security
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K023 - EDGE COMPUTING «HÜCRE» Bant: K021-K120
- Sorumluluk: Edge Runtime · Edge Nodes · Edge Processing · Offline Edge · Edge Lifecycle
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K024 - IDENTITY & ACCESS «DÜĞÜM» Bant: K021-K120
- Sorumluluk: Identity · Authentication · Authorization · Roles · Policies · Federation · Identity Audit
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K025 - TENANT / ORGANIZATION «OMURGA» Bant: K021-K120
- Sorumluluk: Organization · Tenant · Membership · Isolation · Tenant Policy
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K026 - CONFIGURATION «AYNA» Bant: K021-K120
- Sorumluluk: System/Runtime/User Config · Device Config · Validation · Distribution
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K027 - FEATURE MANAGEMENT «PUSULA» Bant: K021-K120
- Sorumluluk: Feature Flags · Rollout · Experimentation · Version Gating · Audit
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K028 - WORKFLOW / ORCHESTRATION «ÇARK» Bant: K021-K120
- Sorumluluk: Definition · Execution · State Machine · Compensation · Failure
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K029 - SCHEDULING «MÜHÜR» Bant: K021-K120
- Sorumluluk: Job/Task Scheduling · Recurrence · Priority · Schedule Recovery
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K030 - EVENT PLATFORM «ZAR» Bant: K021-K120
- Sorumluluk: Event Contract · Routing · Processing · Storage · Delivery · Observability
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K031 - MESSAGE / STREAM «KANTAR» Bant: K021-K120
- Sorumluluk: Producer · Consumer · Queue · Stream · Retry · Dead Letter · Ordering
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K032 - DISTRIBUTED STATE «MEZİT» Bant: K021-K120
- Sorumluluk: Shared/Local State · Replication · Sync · Session State · Recovery
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K033 - SEARCH / DISCOVERY «ALEV» Bant: K021-K120
- Sorumluluk: Indexing · Search · Filtering · Ranking · Faceting · Discovery
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K034 - RECOMMENDATION «BUZUL» Bant: K021-K120
- Sorumluluk: Engine · Candidate Generation · Ranking · Context · User Signals · Feedback
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K035 - PERSONALIZATION «KÖPRÜ» Bant: K021-K120
- Sorumluluk: User Profile · Preferences · Behavior · Personal Models · Privacy Boundary
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K036 - SOCIAL / COLLABORATION «HAN» Bant: K021-K120
- Sorumluluk: Users · Groups · Shared Media · Presence · Social Graph
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K037 - NOTIFICATION «DEPO» Bant: K021-K120
- Sorumluluk: Push · Email · In-App · Device Notification · Delivery · Preference
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K038 - COMMUNICATION «ARENA» Bant: K021-K120
- Sorumluluk: User/System/Device Comm · Real-Time Comm · Policy
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K039 - REAL-TIME PLATFORM «SARNIÇ» Bant: K021-K120
- Sorumluluk: Session · Presence · State Updates · Live Events · Low-Latency Transport
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K040 - SYNCHRONIZATION «HAT» Bant: K021-K120
- Sorumluluk: Device Sync · Media Sync · Offline Sync · Conflict Resolution · Replication
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K041 - COMMERCE «SIĞINAK» Bant: K021-K120
- Sorumluluk: Product · Catalog · Pricing · Cart · Checkout · Order · Entitlement
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K042 - BILLING / PAYMENT «MABET» Bant: K021-K120
- Sorumluluk: Billing · Payment · Invoice · Transaction · Refund · Payment Security
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K043 - SUBSCRIPTION «LİMAN» Bant: K021-K120
- Sorumluluk: Plans · Lifecycle · Renewal · Cancellation · Upgrade/Downgrade · Entitlement
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K044 - ORDER / ENTITLEMENT «OCAK» Bant: K021-K120
- Sorumluluk: Order Management · Access Grant · Access Revocation · Validation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K045 - RIGHTS MANAGEMENT «DİRENÇ» Bant: K021-K120
- Sorumluluk: Ownership · Usage Rights · Content Rights · Regional Rights · Audit
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K046 - LICENSING «PİRAMİT» Bant: K021-K120
- Sorumluluk: License Grant · Validation · Scope · Expiration · Compliance
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K047 - CONTENT MANAGEMENT «SFENKS» Bant: K021-K120
- Sorumluluk: Content · Metadata · Lifecycle · Versioning · Publishing · Moderation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K048 - DIGITAL ASSETS «GÖVDE» Bant: K021-K120
- Sorumluluk: Asset Storage · Metadata · Transformation · Delivery · Integrity
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K049 - MARKETPLACE «KANAL» Bant: K021-K120
- Sorumluluk: Seller · Buyer · Listing · Transaction · Trust
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K050 - CUSTOMER / CRM «MHR» Bant: K021-K120
- Sorumluluk: Customer Profile · Lifecycle · Interaction · Support · Segmentation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K051 - AI PLATFORM «SEDEF» Bant: K021-K120
- Sorumluluk: AI Runtime · Model Runtime · Inference Platform · Model Registry · Observability
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K052 - AI AGENTS «HALKA» Bant: K021-K120
- Sorumluluk: Agent Identity · Runtime · Lifecycle · Memory · Tools · Governance
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K053 - AGENT ORCHESTRATION «ZİRVE» Bant: K021-K120
- Sorumluluk: Selection · Task Delegation · Handoff · Failure Recovery · Policy
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K054 - MODEL MANAGEMENT «TEMEL TAŞI» Bant: K021-K120
- Sorumluluk: Registry · Version · Deployment · Evaluation · Rollback · Lineage
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K055 - INFERENCE «DEMİRHANE» Bant: K021-K120
- Sorumluluk: Request · Runtime · Routing · Policy · Cache · Failure · Audit
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K056 - AI SAFETY «KANAT» Bant: K021-K120
- Sorumluluk: AI Policy · Input/Output Safety · Tool Safety · Abuse Prevention · Risk
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K057 - AI EVALUATION «ÇEKİRDEK» Bant: K021-K120
- Sorumluluk: Benchmark · Quality · Accuracy · Regression · Evidence
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K058 - AI MEMORY «MIKNATIS» Bant: K021-K120
- Sorumluluk: Working Memory · Long-Term · Retrieval · Update · Security · Lifecycle
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K059 - AGENT TOOLS «FENER» Bant: K021-K120
- Sorumluluk: Tool Registry · Discovery · Invocation · Authorization · Failure
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K060 - AGENT SECURITY «ÇELİK KAPI» Bant: K021-K120
- Sorumluluk: Agent AuthZ · Tool Permissions · Data Access · Prompt Security · Isolation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K061 - KNOWLEDGE PLATFORM «SUR» Bant: K021-K120
- Sorumluluk: Knowledge Model · Objects · Relations · Retrieval · Governance
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K062 - KNOWLEDGE GRAPH «KULE» Bant: K021-K120
- Sorumluluk: Graph Model · Nodes · Edges · Traversal · Query · Validation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K063 - RAG «HÜCRE» Bant: K021-K120
- Sorumluluk: Retrieval · Chunking · Embedding · Context Assembly · Grounding · Provenance
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K064 - SEMANTIC / VECTOR SEARCH «DÜĞÜM» Bant: K021-K120
- Sorumluluk: Embedding Space · Vector Index · Similarity · Hybrid Search · Ranking
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K065 - KNOWLEDGE INGESTION «OMURGA» Bant: K021-K120
- Sorumluluk: Source Discovery · Document/Code Ingestion · Normalization · Validation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K066 - KNOWLEDGE VALIDATION «AYNA» Bant: K021-K120
- Sorumluluk: Evidence · Consistency · Conflict Detection · Freshness · Confidence
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K067 - PROVENANCE «PUSULA» Bant: K021-K120
- Sorumluluk: Source Identity · Evidence Chain · Version · Timestamp · Traceability
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K068 - ARCHITECTURE KNOWLEDGE «ÇARK» Bant: K021-K120
- Sorumluluk: Layer/Domain/Component Registry · Dependency Registry · ADR Registry
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K069 - ARCHITECTURE GRAPH «MÜHÜR» Bant: K021-K120
- Sorumluluk: Layer Graph · Dependency Graph · Runtime Graph · Security Graph
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K070 - AGENT KNOWLEDGE «ZAR» Bant: K021-K120
- Sorumluluk: Agent Context · Instructions · Constraints · Evidence · Learning
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K071 - DEVELOPER PLATFORM «KANTAR» Bant: K021-K120
- Sorumluluk: DX · Dev Tools · Templates · Local Environment · Governance
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K072 - SDK / API PLATFORM «MEZİT» Bant: K021-K120
- Sorumluluk: SDK · API Client/Server · Contracts · Versioning · Error Model
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K073 - PLUGIN PLATFORM «ALEV» Bant: K021-K120
- Sorumluluk: Plugin Model · Registry · Lifecycle · Permissions · Isolation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K074 - EXTENSION PLATFORM «BUZUL» Bant: K021-K120
- Sorumluluk: Extension Model · Registry · Extension Points · Security
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K075 - INTEGRATION PLATFORM «KÖPRÜ» Bant: K021-K120
- Sorumluluk: Connector · Adapter · External Service · Contract · Failure Handling
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K076 - DEVELOPER PORTAL «HAN» Bant: K021-K120
- Sorumluluk: Documentation · API Explorer · Examples · Developer Access
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K077 - API GOVERNANCE «DEPO» Bant: K021-K120
- Sorumluluk: Standards · Contract Governance · Version Policy · Deprecation · Audit
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K078 - CONTRACT MANAGEMENT «ARENA» Bant: K021-K120
- Sorumluluk: Definition · Validation · Schema · Compatibility · Evidence
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K079 - VERSION MANAGEMENT «SARNIÇ» Bant: K021-K120
- Sorumluluk: App/API/Schema/Hardware/Firmware/Agent Version · Matrix
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K080 - COMPATIBILITY «HAT» Bant: K021-K120
- Sorumluluk: API/Data/Hardware/Client Compatibility · Backward Compat
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K081 - AUTOMOTIVE «SIĞINAK» Bant: K021-K120
- Sorumluluk: Vehicle Audio · Connectivity · Infotainment · Vehicle AI · Diagnostics
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K082 - VEHICLE AUDIO «MABET» Bant: K021-K120
- Sorumluluk: Cabin DSP · Speaker Layout · Road Noise Compensation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K083 - VEHICLE CONNECTIVITY «LİMAN» Bant: K021-K120
- Sorumluluk: Bluetooth · CarPlay/Android Auto · OTA Link
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K084 - INFOTAINMENT «OCAK» Bant: K021-K120
- Sorumluluk: Head Unit · HMI · Media Sources
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K085 - CAR AI «DİRENÇ» Bant: K021-K120
- Sorumluluk: Voice Assistant · Driver Attention · Contextual Rec.
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K086 - VEHICLE DEVICE CONTROL «PİRAMİT» Bant: K021-K120
- Sorumluluk: CAN Bus · Actuator · Command Safety
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K087 - VEHICLE TELEMETRY «SFENKS» Bant: K021-K120
- Sorumluluk: CAN Logging · Remote Diagnostics
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K088 - VEHICLE SECURITY «GÖVDE» Bant: K021-K120
- Sorumluluk: Secure Boot · Signal Auth · Privacy
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K089 - VEHICLE UPDATE «KANAL» Bant: K021-K120
- Sorumluluk: OTA · A/B Partition · Rollback
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K090 - VEHICLE DIAGNOSTICS «MHR» Bant: K021-K120
- Sorumluluk: DTC · Health Report · Service Mode
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K091 - SMART HOME «SEDEF» Bant: K021-K120
- Sorumluluk: Room Audio · Automation · Scene
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K092 - IoT «HALKA» Bant: K021-K120
- Sorumluluk: MQTT/CoAP · Edge Gateway · Device Data
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K093 - DEVICE MANAGEMENT «ZİRVE» Bant: K021-K120
- Sorumluluk: Registry · Provisioning · Firmware
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K094 - DEVICE DISCOVERY «TEMEL TAŞI» Bant: K021-K120
- Sorumluluk: mDNS/SSDP · Pairing · Topology
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K095 - DEVICE PROVISIONING «DEMİRHANE» Bant: K021-K120
- Sorumluluk: Onboarding · Credential · Attestation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K096 - DEVICE TELEMETRY «KANAT» Bant: K021-K120
- Sorumluluk: Metrics · Health · Usage
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K097 - DEVICE SECURITY «ÇEKİRDEK» Bant: K021-K120
- Sorumluluk: Device Identity · Secure Channel · OTA Sign
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K098 - HOME AUTOMATION «MIKNATIS» Bant: K021-K120
- Sorumluluk: Rules · Triggers · Actuation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K099 - ROOM / ENVIRONMENT «FENER» Bant: K021-K120
- Sorumluluk: Room Acoustics · Ambient Light · Scene
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K100 - MULTI-ROOM «ÇELİK KAPI» Bant: K021-K120
- Sorumluluk: Grouping · Clock Sync · Handoff · Latency Align
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K101 - STUDIO PLATFORM «SUR» Bant: K021-K120
- Sorumluluk: Session · Project · Bounce · Template
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K102 - RECORDING «KULE» Bant: K021-K120
- Sorumluluk: Capture · Input Gain · Take · Punch
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K103 - EDITING «HÜCRE» Bant: K021-K120
- Sorumluluk: Slice · Fade · Crossfade · Region
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K104 - MIXING «DÜĞÜM» Bant: K021-K120
- Sorumluluk: Channel Strip · Bus · Send · Automation
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K105 - MASTERING «OMURGA» Bant: K021-K120
- Sorumluluk: Loudness EBU R128 · Dither · Reference · Export
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K106 - MONITORING «AYNA» Bant: K021-K120
- Sorumluluk: Metering · K-System · Reference Switch
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K107 - AUDIO MEASUREMENT «PUSULA» Bant: K021-K120
- Sorumluluk: THD · SNR · Frequency Response · RTA
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K108 - SPECTRAL ANALYSIS «ÇARK» Bant: K021-K120
- Sorumluluk: FFT · Spectrogram · Waterfall
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K109 - EQUALIZATION «MÜHÜR» Bant: K021-K120
- Sorumluluk: Parametric · Graphic · Linear Phase · Dynamic EQ
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K110 - DYNAMICS PROCESSING «ZAR» Bant: K021-K120
- Sorumluluk: Compressor · Expander · Gate · Multiband
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K111 - SPATIAL AUDIO «KANTAR» Bant: K021-K120
- Sorumluluk: Binaural · HRTF · Ambisonics · Object Audio
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K112 - SURROUND AUDIO «MEZİT» Bant: K021-K120
- Sorumluluk: 5.1/7.1 · Upmix · Downmix · Channel Map
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K113 - AUDIO ROUTING «ALEV» Bant: K021-K120
- Sorumluluk: Matrix · Patch · Sidechain
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K114 - AUDIO SESSION «BUZUL» Bant: K021-K120
- Sorumluluk: Snapshot · Recall · Automation Lane
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K115 - AUDIO PRESETS «KÖPRÜ» Bant: K021-K120
- Sorumluluk: Library · Version · Import/Export
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K116 - AUDIO CALIBRATION «HAN» Bant: K021-K120
- Sorumluluk: Speaker Cal · Room Correction · Mic Cal
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K117 - AUDIO QUALITY «DEPO» Bant: K021-K120
- Sorumluluk: Gapless · Bit-perfect · Sample-rate Match
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K118 - AUDIO EXPORT «ARENA» Bant: K021-K120
- Sorumluluk: Render · Stem · Bounce Options
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K119 - PROFESSIONAL WORKFLOW «SARNIÇ» Bant: K021-K120
- Sorumluluk: Control Surface · MIDI Map · Shortcut
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=ORTA · failure=fail-open (ADR gerekli) · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

### K120 - PRO AUDIO INTEGRATION «HAT» Bant: K021-K120
- Sorumluluk: Plugin Host · External Sync · External Clock
- Sınır: data=alan verisi (başka katmanla PAYLAŞMAZ) · security=YÜKSEK · failure=fail-secure · izinli=K000-K020 çekirdeği + bu bant altı · yasak=K010 UI'dan doğrudan veri katmanı, geriye çağrı
- Kanıt: kaynak prompt K021–K120 blokları · web research (EK B)

## A.3 — GENİŞLEME / DERİNLİK / GELECEK KATMANLARI (K121 → K5999)

> BANT 132-157 (K4981 > K5999) blok diyagramı — bant adları A.3 başlıklarından birebir.

```text
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 132  K4981 > K5020                │   │BANT 133  K5021 > K5060                │
│ENTERPRISE - SCALE OUT «ÖLÇEKLENME»    │   │ENTERPRISE - RESILIENCE «DAYANIKLILIK» │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 134  K5061 > K5100                │   │BANT 135  K5101 > K5140                │
│ENTERPRISE - CHAOS «KAOS MÜHENDİSLİĞİ» │   │ENTERPRISE - CAPACITY «KAPASİTE»       │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 136  K5141 > K5180                │   │BANT 137  K5181 > K5220                │
│FUTURE - SPATIAL «UZAMSAL BİLGİSAYAR»  │   │FUTURE - AVATARS «AVATAR»              │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 138  K5221 > K5260                │   │BANT 139  K5261 > K5300                │
│FUTURE - CLOUD GAMING «BULUT OYUN»     │   │FUTURE - HEALTH AUDIO «SAĞLIK SESİ»    │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 140  K5301 > K5340                │   │BANT 141  K5341 > K5380                │
│FUTURE - EDUCATION «EĞİTİM»            │   │FUTURE - TELECOM «TELEKOM»             │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 142  K5381 > K5420                │   │BANT 143  K5421 > K5460                │
│FUTURE - PAYMENTS WALLET «CUZDAN»      │   │FUTURE - SMART CITY SES «ŞEHİR SES AĞI»│
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 144  K5461 > K5500                │   │BANT 145  K5501 > K5540                │
│FUTURE - ROBOTICS AUDIO «ROBOTİK SES»  │   │EXPANSION - RESERVED 1 «REZERVE 1»     │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 146  K5541 > K5580                │   │BANT 147  K5581 > K5620                │
│EXPANSION - RESERVED 2 «REZERVE 2»     │   │EXPANSION - RESERVED 3 «REZERVE 3»     │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 148  K5621 > K5660                │   │BANT 149  K5661 > K5700                │
│EXPANSION - RESERVED 4 «REZERVE 4»     │   │EXPANSION - RESERVED 5 «REZERVE 5»     │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 150  K5701 > K5740                │   │BANT 151  K5741 > K5780                │
│EXPANSION - RESERVED 6 «REZERVE 6»     │   │EXPANSION - RESERVED 7 «REZERVE 7»     │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 152  K5781 > K5820                │   │BANT 153  K5821 > K5860                │
│EXPANSION - RESERVED 8 «REZERVE 8»     │   │EXPANSION - RESERVED 9 «REZERVE 9»     │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 154  K5861 > K5900                │   │BANT 155  K5901 > K5940                │
│EXPANSION - RESERVED 10 «REZERVE 10»   │   │EXPANSION - RESERVED 11 «REZERVE 11»   │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
┌───────────────────────────────────────┐   ┌───────────────────────────────────────┐
│BANT 156  K5941 > K5980                │   │BANT 157  K5981 > K5999                │
│EXPANSION - RESERVED 12 «REZERVE 12»   │   │EXPANSION - RESERVED 13 «REZERVE 13»   │
└───────────────────────────────────────┘   └───────────────────────────────────────┘
```

### BANT 001 - BROADCAST / LIVE AUDIO «YAYIN KULESİ» K121 > K140

#### K121 - BROADCAST / LIVE AUDIO «DEMİRHANE»
- Sorumluluk: Live Console - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K122 - BROADCAST / LIVE AUDIO «KANAT»
- Sorumluluk: Redundancy - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K123 - BROADCAST / LIVE AUDIO «ÇEKİRDEK»
- Sorumluluk: Talkback - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K124 - BROADCAST / LIVE AUDIO «MIKNATIS»
- Sorumluluk: Delay Rail - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K125 - BROADCAST / LIVE AUDIO «FENER»
- Sorumluluk: On-Air Lock - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K126 - BROADCAST / LIVE AUDIO «ÇELİK KAPI»
- Sorumluluk: Stream Encoder - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K127 - BROADCAST / LIVE AUDIO «SUR»
- Sorumluluk: Live Console - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K128 - BROADCAST / LIVE AUDIO «KULE»
- Sorumluluk: Redundancy - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K129 - BROADCAST / LIVE AUDIO «HÜCRE»
- Sorumluluk: Talkback - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K130 - BROADCAST / LIVE AUDIO «DÜĞÜM»
- Sorumluluk: Delay Rail - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K131 - BROADCAST / LIVE AUDIO «OMURGA»
- Sorumluluk: On-Air Lock - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K132 - BROADCAST / LIVE AUDIO «AYNA»
- Sorumluluk: Stream Encoder - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K133 - BROADCAST / LIVE AUDIO «PUSULA»
- Sorumluluk: Live Console - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K134 - BROADCAST / LIVE AUDIO «ÇARK»
- Sorumluluk: Redundancy - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K135 - BROADCAST / LIVE AUDIO «MÜHÜR»
- Sorumluluk: Talkback - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K136 - BROADCAST / LIVE AUDIO «ZAR»
- Sorumluluk: Delay Rail - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K137 - BROADCAST / LIVE AUDIO «KANTAR»
- Sorumluluk: On-Air Lock - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K138 - BROADCAST / LIVE AUDIO «MEZİT»
- Sorumluluk: Stream Encoder - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K139 - BROADCAST / LIVE AUDIO «ALEV»
- Sorumluluk: Live Console - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K140 - BROADCAST / LIVE AUDIO «BUZUL»
- Sorumluluk: Redundancy - BROADCAST / LIVE AUDIO kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K120 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 002 - MEDIA PRODUCTION «STÜDYO ATÖLYESİ» K141 > K160

#### K141 - MEDIA PRODUCTION «KÖPRÜ»
- Sorumluluk: Project Template - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K142 - MEDIA PRODUCTION «HAN»
- Sorumluluk: Asset Bin - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K143 - MEDIA PRODUCTION «DEPO»
- Sorumluluk: Render Farm - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K144 - MEDIA PRODUCTION «ARENA»
- Sorumluluk: Version Stack - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K145 - MEDIA PRODUCTION «SARNIÇ»
- Sorumluluk: Review Loop - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K146 - MEDIA PRODUCTION «HAT»
- Sorumluluk: Project Template - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K147 - MEDIA PRODUCTION «SIĞINAK»
- Sorumluluk: Asset Bin - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K148 - MEDIA PRODUCTION «MABET»
- Sorumluluk: Render Farm - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K149 - MEDIA PRODUCTION «LİMAN»
- Sorumluluk: Version Stack - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K150 - MEDIA PRODUCTION «OCAK»
- Sorumluluk: Review Loop - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K151 - MEDIA PRODUCTION «DİRENÇ»
- Sorumluluk: Project Template - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K152 - MEDIA PRODUCTION «PİRAMİT»
- Sorumluluk: Asset Bin - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K153 - MEDIA PRODUCTION «SFENKS»
- Sorumluluk: Render Farm - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K154 - MEDIA PRODUCTION «GÖVDE»
- Sorumluluk: Version Stack - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K155 - MEDIA PRODUCTION «KANAL»
- Sorumluluk: Review Loop - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K156 - MEDIA PRODUCTION «MHR»
- Sorumluluk: Project Template - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K157 - MEDIA PRODUCTION «SEDEF»
- Sorumluluk: Asset Bin - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K158 - MEDIA PRODUCTION «HALKA»
- Sorumluluk: Render Farm - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K159 - MEDIA PRODUCTION «ZİRVE»
- Sorumluluk: Version Stack - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K160 - MEDIA PRODUCTION «TEMEL TAŞI»
- Sorumluluk: Review Loop - MEDIA PRODUCTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K140 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 003 - MEDIA DISTRIBUTION «DAGITIM LİMANI» K161 > K180

#### K161 - MEDIA DISTRIBUTION «DEMİRHANE»
- Sorumluluk: Manifest - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K162 - MEDIA DISTRIBUTION «KANAT»
- Sorumluluk: CDN Edge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K163 - MEDIA DISTRIBUTION «ÇEKİRDEK»
- Sorumluluk: Multi-CDN - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K164 - MEDIA DISTRIBUTION «MIKNATIS»
- Sorumluluk: Origin Shield - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K165 - MEDIA DISTRIBUTION «FENER»
- Sorumluluk: Purge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K166 - MEDIA DISTRIBUTION «ÇELİK KAPI»
- Sorumluluk: Manifest - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K167 - MEDIA DISTRIBUTION «SUR»
- Sorumluluk: CDN Edge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K168 - MEDIA DISTRIBUTION «KULE»
- Sorumluluk: Multi-CDN - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K169 - MEDIA DISTRIBUTION «HÜCRE»
- Sorumluluk: Origin Shield - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K170 - MEDIA DISTRIBUTION «DÜĞÜM»
- Sorumluluk: Purge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K171 - MEDIA DISTRIBUTION «OMURGA»
- Sorumluluk: Manifest - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K172 - MEDIA DISTRIBUTION «AYNA»
- Sorumluluk: CDN Edge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K173 - MEDIA DISTRIBUTION «PUSULA»
- Sorumluluk: Multi-CDN - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K174 - MEDIA DISTRIBUTION «ÇARK»
- Sorumluluk: Origin Shield - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K175 - MEDIA DISTRIBUTION «MÜHÜR»
- Sorumluluk: Purge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K176 - MEDIA DISTRIBUTION «ZAR»
- Sorumluluk: Manifest - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K177 - MEDIA DISTRIBUTION «KANTAR»
- Sorumluluk: CDN Edge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K178 - MEDIA DISTRIBUTION «MEZİT»
- Sorumluluk: Multi-CDN - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K179 - MEDIA DISTRIBUTION «ALEV»
- Sorumluluk: Origin Shield - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K180 - MEDIA DISTRIBUTION «BUZUL»
- Sorumluluk: Purge - MEDIA DISTRIBUTION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K160 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 004 - CONTENT INTELLIGENCE «İÇERİK GÖZLEMCİSİ» K181 > K200

#### K181 - CONTENT INTELLIGENCE «KÖPRÜ»
- Sorumluluk: Transcript - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K182 - CONTENT INTELLIGENCE «HAN»
- Sorumluluk: Fingerprint - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K183 - CONTENT INTELLIGENCE «DEPO»
- Sorumluluk: Content ID - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K184 - CONTENT INTELLIGENCE «ARENA»
- Sorumluluk: Auto Tag - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K185 - CONTENT INTELLIGENCE «SARNIÇ»
- Sorumluluk: Highlight - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K186 - CONTENT INTELLIGENCE «HAT»
- Sorumluluk: Transcript - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K187 - CONTENT INTELLIGENCE «SIĞINAK»
- Sorumluluk: Fingerprint - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K188 - CONTENT INTELLIGENCE «MABET»
- Sorumluluk: Content ID - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K189 - CONTENT INTELLIGENCE «LİMAN»
- Sorumluluk: Auto Tag - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K190 - CONTENT INTELLIGENCE «OCAK»
- Sorumluluk: Highlight - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K191 - CONTENT INTELLIGENCE «DİRENÇ»
- Sorumluluk: Transcript - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K192 - CONTENT INTELLIGENCE «PİRAMİT»
- Sorumluluk: Fingerprint - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K193 - CONTENT INTELLIGENCE «SFENKS»
- Sorumluluk: Content ID - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K194 - CONTENT INTELLIGENCE «GÖVDE»
- Sorumluluk: Auto Tag - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K195 - CONTENT INTELLIGENCE «KANAL»
- Sorumluluk: Highlight - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K196 - CONTENT INTELLIGENCE «MHR»
- Sorumluluk: Transcript - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K197 - CONTENT INTELLIGENCE «SEDEF»
- Sorumluluk: Fingerprint - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K198 - CONTENT INTELLIGENCE «HALKA»
- Sorumluluk: Content ID - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K199 - CONTENT INTELLIGENCE «ZİRVE»
- Sorumluluk: Auto Tag - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K200 - CONTENT INTELLIGENCE «TEMEL TAŞI»
- Sorumluluk: Highlight - CONTENT INTELLIGENCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K180 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 005 - DIGITAL TWIN «AYNA DÜNYA» K201 > K220

#### K201 - DIGITAL TWIN «DEMİRHANE»
- Sorumluluk: Twin Model - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K202 - DIGITAL TWIN «KANAT»
- Sorumluluk: State Sync - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K203 - DIGITAL TWIN «ÇEKİRDEK»
- Sorumluluk: Telemetry Mirror - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K204 - DIGITAL TWIN «MIKNATIS»
- Sorumluluk: Drift Detect - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K205 - DIGITAL TWIN «FENER»
- Sorumluluk: Twin Model - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K206 - DIGITAL TWIN «ÇELİK KAPI»
- Sorumluluk: State Sync - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K207 - DIGITAL TWIN «SUR»
- Sorumluluk: Telemetry Mirror - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K208 - DIGITAL TWIN «KULE»
- Sorumluluk: Drift Detect - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K209 - DIGITAL TWIN «HÜCRE»
- Sorumluluk: Twin Model - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K210 - DIGITAL TWIN «DÜĞÜM»
- Sorumluluk: State Sync - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K211 - DIGITAL TWIN «OMURGA»
- Sorumluluk: Telemetry Mirror - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K212 - DIGITAL TWIN «AYNA»
- Sorumluluk: Drift Detect - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K213 - DIGITAL TWIN «PUSULA»
- Sorumluluk: Twin Model - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K214 - DIGITAL TWIN «ÇARK»
- Sorumluluk: State Sync - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K215 - DIGITAL TWIN «MÜHÜR»
- Sorumluluk: Telemetry Mirror - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K216 - DIGITAL TWIN «ZAR»
- Sorumluluk: Drift Detect - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K217 - DIGITAL TWIN «KANTAR»
- Sorumluluk: Twin Model - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K218 - DIGITAL TWIN «MEZİT»
- Sorumluluk: State Sync - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K219 - DIGITAL TWIN «ALEV»
- Sorumluluk: Telemetry Mirror - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K220 - DIGITAL TWIN «BUZUL»
- Sorumluluk: Drift Detect - DIGITAL TWIN kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K200 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 006 - SIMULATION «SİMÜLASYON HATTI» K221 > K240

#### K221 - SIMULATION «KÖPRÜ»
- Sorumluluk: Scenario - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K222 - SIMULATION «HAN»
- Sorumluluk: Load Sim - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K223 - SIMULATION «DEPO»
- Sorumluluk: Fault Injection - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K224 - SIMULATION «ARENA»
- Sorumluluk: Replay - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K225 - SIMULATION «SARNIÇ»
- Sorumluluk: Sandbox - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K226 - SIMULATION «HAT»
- Sorumluluk: Scenario - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K227 - SIMULATION «SIĞINAK»
- Sorumluluk: Load Sim - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K228 - SIMULATION «MABET»
- Sorumluluk: Fault Injection - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K229 - SIMULATION «LİMAN»
- Sorumluluk: Replay - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K230 - SIMULATION «OCAK»
- Sorumluluk: Sandbox - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K231 - SIMULATION «DİRENÇ»
- Sorumluluk: Scenario - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K232 - SIMULATION «PİRAMİT»
- Sorumluluk: Load Sim - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K233 - SIMULATION «SFENKS»
- Sorumluluk: Fault Injection - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K234 - SIMULATION «GÖVDE»
- Sorumluluk: Replay - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K235 - SIMULATION «KANAL»
- Sorumluluk: Sandbox - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K236 - SIMULATION «MHR»
- Sorumluluk: Scenario - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K237 - SIMULATION «SEDEF»
- Sorumluluk: Load Sim - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K238 - SIMULATION «HALKA»
- Sorumluluk: Fault Injection - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K239 - SIMULATION «ZİRVE»
- Sorumluluk: Replay - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K240 - SIMULATION «TEMEL TAŞI»
- Sorumluluk: Sandbox - SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K220 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 007 - HARDWARE SIMULATION «DONANIM SİMÜLASYONU» K241 > K260

#### K241 - HARDWARE SIMULATION «DEMİRHANE»
- Sorumluluk: SPICE - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K242 - HARDWARE SIMULATION «KANAT»
- Sorumluluk: Signal Chain Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K243 - HARDWARE SIMULATION «ÇEKİRDEK»
- Sorumluluk: Power Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K244 - HARDWARE SIMULATION «MIKNATIS»
- Sorumluluk: Thermal Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K245 - HARDWARE SIMULATION «FENER»
- Sorumluluk: SPICE - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K246 - HARDWARE SIMULATION «ÇELİK KAPI»
- Sorumluluk: Signal Chain Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K247 - HARDWARE SIMULATION «SUR»
- Sorumluluk: Power Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K248 - HARDWARE SIMULATION «KULE»
- Sorumluluk: Thermal Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K249 - HARDWARE SIMULATION «HÜCRE»
- Sorumluluk: SPICE - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K250 - HARDWARE SIMULATION «DÜĞÜM»
- Sorumluluk: Signal Chain Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K251 - HARDWARE SIMULATION «OMURGA»
- Sorumluluk: Power Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K252 - HARDWARE SIMULATION «AYNA»
- Sorumluluk: Thermal Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K253 - HARDWARE SIMULATION «PUSULA»
- Sorumluluk: SPICE - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K254 - HARDWARE SIMULATION «ÇARK»
- Sorumluluk: Signal Chain Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K255 - HARDWARE SIMULATION «MÜHÜR»
- Sorumluluk: Power Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K256 - HARDWARE SIMULATION «ZAR»
- Sorumluluk: Thermal Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K257 - HARDWARE SIMULATION «KANTAR»
- Sorumluluk: SPICE - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K258 - HARDWARE SIMULATION «MEZİT»
- Sorumluluk: Signal Chain Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K259 - HARDWARE SIMULATION «ALEV»
- Sorumluluk: Power Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K260 - HARDWARE SIMULATION «BUZUL»
- Sorumluluk: Thermal Sim - HARDWARE SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K240 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 008 - AUDIO SIMULATION «SES SİMÜLASYONU» K261 > K280

#### K261 - AUDIO SIMULATION «KÖPRÜ»
- Sorumluluk: Room Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K262 - AUDIO SIMULATION «HAN»
- Sorumluluk: Speaker Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K263 - AUDIO SIMULATION «DEPO»
- Sorumluluk: Codec Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K264 - AUDIO SIMULATION «ARENA»
- Sorumluluk: Latency Budget - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K265 - AUDIO SIMULATION «SARNIÇ»
- Sorumluluk: Room Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K266 - AUDIO SIMULATION «HAT»
- Sorumluluk: Speaker Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K267 - AUDIO SIMULATION «SIĞINAK»
- Sorumluluk: Codec Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K268 - AUDIO SIMULATION «MABET»
- Sorumluluk: Latency Budget - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K269 - AUDIO SIMULATION «LİMAN»
- Sorumluluk: Room Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K270 - AUDIO SIMULATION «OCAK»
- Sorumluluk: Speaker Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K271 - AUDIO SIMULATION «DİRENÇ»
- Sorumluluk: Codec Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K272 - AUDIO SIMULATION «PİRAMİT»
- Sorumluluk: Latency Budget - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K273 - AUDIO SIMULATION «SFENKS»
- Sorumluluk: Room Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K274 - AUDIO SIMULATION «GÖVDE»
- Sorumluluk: Speaker Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K275 - AUDIO SIMULATION «KANAL»
- Sorumluluk: Codec Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K276 - AUDIO SIMULATION «MHR»
- Sorumluluk: Latency Budget - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K277 - AUDIO SIMULATION «SEDEF»
- Sorumluluk: Room Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K278 - AUDIO SIMULATION «HALKA»
- Sorumluluk: Speaker Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K279 - AUDIO SIMULATION «ZİRVE»
- Sorumluluk: Codec Sim - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K280 - AUDIO SIMULATION «TEMEL TAŞI»
- Sorumluluk: Latency Budget - AUDIO SIMULATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K260 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 009 - SYSTEM MODELING «SİSTEM MODELİ» K281 > K300

#### K281 - SYSTEM MODELING «DEMİRHANE»
- Sorumluluk: Model Graph - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K282 - SYSTEM MODELING «KANAT»
- Sorumluluk: Constraint - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K283 - SYSTEM MODELING «ÇEKİRDEK»
- Sorumluluk: Trade-off - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K284 - SYSTEM MODELING «MIKNATIS»
- Sorumluluk: Sizing - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K285 - SYSTEM MODELING «FENER»
- Sorumluluk: Model Graph - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K286 - SYSTEM MODELING «ÇELİK KAPI»
- Sorumluluk: Constraint - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K287 - SYSTEM MODELING «SUR»
- Sorumluluk: Trade-off - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K288 - SYSTEM MODELING «KULE»
- Sorumluluk: Sizing - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K289 - SYSTEM MODELING «HÜCRE»
- Sorumluluk: Model Graph - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K290 - SYSTEM MODELING «DÜĞÜM»
- Sorumluluk: Constraint - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K291 - SYSTEM MODELING «OMURGA»
- Sorumluluk: Trade-off - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K292 - SYSTEM MODELING «AYNA»
- Sorumluluk: Sizing - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K293 - SYSTEM MODELING «PUSULA»
- Sorumluluk: Model Graph - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K294 - SYSTEM MODELING «ÇARK»
- Sorumluluk: Constraint - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K295 - SYSTEM MODELING «MÜHÜR»
- Sorumluluk: Trade-off - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K296 - SYSTEM MODELING «ZAR»
- Sorumluluk: Sizing - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K297 - SYSTEM MODELING «KANTAR»
- Sorumluluk: Model Graph - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K298 - SYSTEM MODELING «MEZİT»
- Sorumluluk: Constraint - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K299 - SYSTEM MODELING «ALEV»
- Sorumluluk: Trade-off - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K300 - SYSTEM MODELING «BUZUL»
- Sorumluluk: Sizing - SYSTEM MODELING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K280 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 010 - RESEARCH / R&D «ARAŞTIRMA KANADI» K301 > K320

#### K301 - RESEARCH / R&D «KÖPRÜ»
- Sorumluluk: Hypothesis - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K302 - RESEARCH / R&D «HAN»
- Sorumluluk: Experiment - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K303 - RESEARCH / R&D «DEPO»
- Sorumluluk: Lab Note - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K304 - RESEARCH / R&D «ARENA»
- Sorumluluk: Replication - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K305 - RESEARCH / R&D «SARNIÇ»
- Sorumluluk: Hypothesis - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K306 - RESEARCH / R&D «HAT»
- Sorumluluk: Experiment - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K307 - RESEARCH / R&D «SIĞINAK»
- Sorumluluk: Lab Note - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K308 - RESEARCH / R&D «MABET»
- Sorumluluk: Replication - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K309 - RESEARCH / R&D «LİMAN»
- Sorumluluk: Hypothesis - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K310 - RESEARCH / R&D «OCAK»
- Sorumluluk: Experiment - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K311 - RESEARCH / R&D «DİRENÇ»
- Sorumluluk: Lab Note - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K312 - RESEARCH / R&D «PİRAMİT»
- Sorumluluk: Replication - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K313 - RESEARCH / R&D «SFENKS»
- Sorumluluk: Hypothesis - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K314 - RESEARCH / R&D «GÖVDE»
- Sorumluluk: Experiment - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K315 - RESEARCH / R&D «KANAL»
- Sorumluluk: Lab Note - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K316 - RESEARCH / R&D «MHR»
- Sorumluluk: Replication - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K317 - RESEARCH / R&D «SEDEF»
- Sorumluluk: Hypothesis - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K318 - RESEARCH / R&D «HALKA»
- Sorumluluk: Experiment - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K319 - RESEARCH / R&D «ZİRVE»
- Sorumluluk: Lab Note - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K320 - RESEARCH / R&D «TEMEL TAŞI»
- Sorumluluk: Replication - RESEARCH / R&D kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K300 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 011 - EXPERIMENTATION «DENEME SAHASI» K321 > K340

#### K321 - EXPERIMENTATION «DEMİRHANE»
- Sorumluluk: A/B - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K322 - EXPERIMENTATION «KANAT»
- Sorumluluk: Canary - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K323 - EXPERIMENTATION «ÇEKİRDEK»
- Sorumluluk: Feature Lab - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K324 - EXPERIMENTATION «MIKNATIS»
- Sorumluluk: Readout - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K325 - EXPERIMENTATION «FENER»
- Sorumluluk: A/B - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K326 - EXPERIMENTATION «ÇELİK KAPI»
- Sorumluluk: Canary - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K327 - EXPERIMENTATION «SUR»
- Sorumluluk: Feature Lab - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K328 - EXPERIMENTATION «KULE»
- Sorumluluk: Readout - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K329 - EXPERIMENTATION «HÜCRE»
- Sorumluluk: A/B - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K330 - EXPERIMENTATION «DÜĞÜM»
- Sorumluluk: Canary - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K331 - EXPERIMENTATION «OMURGA»
- Sorumluluk: Feature Lab - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K332 - EXPERIMENTATION «AYNA»
- Sorumluluk: Readout - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K333 - EXPERIMENTATION «PUSULA»
- Sorumluluk: A/B - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K334 - EXPERIMENTATION «ÇARK»
- Sorumluluk: Canary - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K335 - EXPERIMENTATION «MÜHÜR»
- Sorumluluk: Feature Lab - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K336 - EXPERIMENTATION «ZAR»
- Sorumluluk: Readout - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K337 - EXPERIMENTATION «KANTAR»
- Sorumluluk: A/B - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K338 - EXPERIMENTATION «MEZİT»
- Sorumluluk: Canary - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K339 - EXPERIMENTATION «ALEV»
- Sorumluluk: Feature Lab - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K340 - EXPERIMENTATION «BUZUL»
- Sorumluluk: Readout - EXPERIMENTATION kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K320 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 012 - BENCHMARKING «ÖLÇÜM KULESİ» K341 > K360

#### K341 - BENCHMARKING «KÖPRÜ»
- Sorumluluk: Benchmark - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K342 - BENCHMARKING «HAN»
- Sorumluluk: Baseline - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K343 - BENCHMARKING «DEPO»
- Sorumluluk: Regression Gate - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K344 - BENCHMARKING «ARENA»
- Sorumluluk: Report - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K345 - BENCHMARKING «SARNIÇ»
- Sorumluluk: Benchmark - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K346 - BENCHMARKING «HAT»
- Sorumluluk: Baseline - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K347 - BENCHMARKING «SIĞINAK»
- Sorumluluk: Regression Gate - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K348 - BENCHMARKING «MABET»
- Sorumluluk: Report - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K349 - BENCHMARKING «LİMAN»
- Sorumluluk: Benchmark - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K350 - BENCHMARKING «OCAK»
- Sorumluluk: Baseline - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K351 - BENCHMARKING «DİRENÇ»
- Sorumluluk: Regression Gate - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K352 - BENCHMARKING «PİRAMİT»
- Sorumluluk: Report - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K353 - BENCHMARKING «SFENKS»
- Sorumluluk: Benchmark - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K354 - BENCHMARKING «GÖVDE»
- Sorumluluk: Baseline - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K355 - BENCHMARKING «KANAL»
- Sorumluluk: Regression Gate - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K356 - BENCHMARKING «MHR»
- Sorumluluk: Report - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K357 - BENCHMARKING «SEDEF»
- Sorumluluk: Benchmark - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K358 - BENCHMARKING «HALKA»
- Sorumluluk: Baseline - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K359 - BENCHMARKING «ZİRVE»
- Sorumluluk: Regression Gate - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K360 - BENCHMARKING «TEMEL TAŞI»
- Sorumluluk: Report - BENCHMARKING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K340 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 013 - SCIENTIFIC COMPUTING «BİLİMSEL HESAP» K361 > K380

#### K361 - SCIENTIFIC COMPUTING «DEMİRHANE»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K362 - SCIENTIFIC COMPUTING «KANAT»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K363 - SCIENTIFIC COMPUTING «ÇEKİRDEK»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K364 - SCIENTIFIC COMPUTING «MIKNATIS»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K365 - SCIENTIFIC COMPUTING «FENER»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K366 - SCIENTIFIC COMPUTING «ÇELİK KAPI»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K367 - SCIENTIFIC COMPUTING «SUR»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K368 - SCIENTIFIC COMPUTING «KULE»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K369 - SCIENTIFIC COMPUTING «HÜCRE»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K370 - SCIENTIFIC COMPUTING «DÜĞÜM»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K371 - SCIENTIFIC COMPUTING «OMURGA»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K372 - SCIENTIFIC COMPUTING «AYNA»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K373 - SCIENTIFIC COMPUTING «PUSULA»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K374 - SCIENTIFIC COMPUTING «ÇARK»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K375 - SCIENTIFIC COMPUTING «MÜHÜR»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K376 - SCIENTIFIC COMPUTING «ZAR»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K377 - SCIENTIFIC COMPUTING «KANTAR»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K378 - SCIENTIFIC COMPUTING «MEZİT»
- Sorumluluk: Determinism - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K379 - SCIENTIFIC COMPUTING «ALEV»
- Sorumluluk: Vectorized Compute - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K380 - SCIENTIFIC COMPUTING «BUZUL»
- Sorumluluk: Numerics - SCIENTIFIC COMPUTING kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K360 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 014 - DATA SCIENCE / ANALYTICS «VERİ GÖZLEMEVİ» K381 > K400

#### K381 - DATA SCIENCE / ANALYTICS «KÖPRÜ»
- Sorumluluk: ETL - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K382 - DATA SCIENCE / ANALYTICS «HAN»
- Sorumluluk: OLAP - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K383 - DATA SCIENCE / ANALYTICS «DEPO»
- Sorumluluk: Cohort - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K384 - DATA SCIENCE / ANALYTICS «ARENA»
- Sorumluluk: Metric Tree - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K385 - DATA SCIENCE / ANALYTICS «SARNIÇ»
- Sorumluluk: Dashboard - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K386 - DATA SCIENCE / ANALYTICS «HAT»
- Sorumluluk: ETL - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K387 - DATA SCIENCE / ANALYTICS «SIĞINAK»
- Sorumluluk: OLAP - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K388 - DATA SCIENCE / ANALYTICS «MABET»
- Sorumluluk: Cohort - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K389 - DATA SCIENCE / ANALYTICS «LİMAN»
- Sorumluluk: Metric Tree - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K390 - DATA SCIENCE / ANALYTICS «OCAK»
- Sorumluluk: Dashboard - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K391 - DATA SCIENCE / ANALYTICS «DİRENÇ»
- Sorumluluk: ETL - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K392 - DATA SCIENCE / ANALYTICS «PİRAMİT»
- Sorumluluk: OLAP - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K393 - DATA SCIENCE / ANALYTICS «SFENKS»
- Sorumluluk: Cohort - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K394 - DATA SCIENCE / ANALYTICS «GÖVDE»
- Sorumluluk: Metric Tree - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K395 - DATA SCIENCE / ANALYTICS «KANAL»
- Sorumluluk: Dashboard - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K396 - DATA SCIENCE / ANALYTICS «MHR»
- Sorumluluk: ETL - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K397 - DATA SCIENCE / ANALYTICS «SEDEF»
- Sorumluluk: OLAP - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K398 - DATA SCIENCE / ANALYTICS «HALKA»
- Sorumluluk: Cohort - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K399 - DATA SCIENCE / ANALYTICS «ZİRVE»
- Sorumluluk: Metric Tree - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K400 - DATA SCIENCE / ANALYTICS «TEMEL TAŞI»
- Sorumluluk: Dashboard - DATA SCIENCE / ANALYTICS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K380 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 015 - GOVERNANCE «YÖNETİŞİM MECLİSİ» K401 > K420

#### K401 - GOVERNANCE «DEMİRHANE»
- Sorumluluk: Policy - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K402 - GOVERNANCE «KANAT»
- Sorumluluk: ADR - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K403 - GOVERNANCE «ÇEKİRDEK»
- Sorumluluk: Review Board - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K404 - GOVERNANCE «MIKNATIS»
- Sorumluluk: Standard - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K405 - GOVERNANCE «FENER»
- Sorumluluk: Policy - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K406 - GOVERNANCE «ÇELİK KAPI»
- Sorumluluk: ADR - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K407 - GOVERNANCE «SUR»
- Sorumluluk: Review Board - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K408 - GOVERNANCE «KULE»
- Sorumluluk: Standard - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K409 - GOVERNANCE «HÜCRE»
- Sorumluluk: Policy - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K410 - GOVERNANCE «DÜĞÜM»
- Sorumluluk: ADR - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K411 - GOVERNANCE «OMURGA»
- Sorumluluk: Review Board - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K412 - GOVERNANCE «AYNA»
- Sorumluluk: Standard - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K413 - GOVERNANCE «PUSULA»
- Sorumluluk: Policy - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K414 - GOVERNANCE «ÇARK»
- Sorumluluk: ADR - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K415 - GOVERNANCE «MÜHÜR»
- Sorumluluk: Review Board - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K416 - GOVERNANCE «ZAR»
- Sorumluluk: Standard - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K417 - GOVERNANCE «KANTAR»
- Sorumluluk: Policy - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K418 - GOVERNANCE «MEZİT»
- Sorumluluk: ADR - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K419 - GOVERNANCE «ALEV»
- Sorumluluk: Review Board - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K420 - GOVERNANCE «BUZUL»
- Sorumluluk: Standard - GOVERNANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K400 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 016 - COMPLIANCE «UYUM KONTROLÜ» K421 > K440

#### K421 - COMPLIANCE «KÖPRÜ»
- Sorumluluk: GDPR - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K422 - COMPLIANCE «HAN»
- Sorumluluk: KVKK - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K423 - COMPLIANCE «DEPO»
- Sorumluluk: License Scan - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K424 - COMPLIANCE «ARENA»
- Sorumluluk: Evidence - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K425 - COMPLIANCE «SARNIÇ»
- Sorumluluk: GDPR - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K426 - COMPLIANCE «HAT»
- Sorumluluk: KVKK - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K427 - COMPLIANCE «SIĞINAK»
- Sorumluluk: License Scan - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K428 - COMPLIANCE «MABET»
- Sorumluluk: Evidence - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K429 - COMPLIANCE «LİMAN»
- Sorumluluk: GDPR - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K430 - COMPLIANCE «OCAK»
- Sorumluluk: KVKK - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K431 - COMPLIANCE «DİRENÇ»
- Sorumluluk: License Scan - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K432 - COMPLIANCE «PİRAMİT»
- Sorumluluk: Evidence - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K433 - COMPLIANCE «SFENKS»
- Sorumluluk: GDPR - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K434 - COMPLIANCE «GÖVDE»
- Sorumluluk: KVKK - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K435 - COMPLIANCE «KANAL»
- Sorumluluk: License Scan - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K436 - COMPLIANCE «MHR»
- Sorumluluk: Evidence - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K437 - COMPLIANCE «SEDEF»
- Sorumluluk: GDPR - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K438 - COMPLIANCE «HALKA»
- Sorumluluk: KVKK - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K439 - COMPLIANCE «ZİRVE»
- Sorumluluk: License Scan - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K440 - COMPLIANCE «TEMEL TAŞI»
- Sorumluluk: Evidence - COMPLIANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K420 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 017 - AUDIT / ASSURANCE «DENETİM KURULU» K441 > K460

#### K441 - AUDIT / ASSURANCE «DEMİRHANE»
- Sorumluluk: Internal Audit - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K442 - AUDIT / ASSURANCE «KANAT»
- Sorumluluk: Trace - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K443 - AUDIT / ASSURANCE «ÇEKİRDEK»
- Sorumluluk: Finding - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K444 - AUDIT / ASSURANCE «MIKNATIS»
- Sorumluluk: CAPA - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K445 - AUDIT / ASSURANCE «FENER»
- Sorumluluk: Internal Audit - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K446 - AUDIT / ASSURANCE «ÇELİK KAPI»
- Sorumluluk: Trace - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K447 - AUDIT / ASSURANCE «SUR»
- Sorumluluk: Finding - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K448 - AUDIT / ASSURANCE «KULE»
- Sorumluluk: CAPA - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K449 - AUDIT / ASSURANCE «HÜCRE»
- Sorumluluk: Internal Audit - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K450 - AUDIT / ASSURANCE «DÜĞÜM»
- Sorumluluk: Trace - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K451 - AUDIT / ASSURANCE «OMURGA»
- Sorumluluk: Finding - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K452 - AUDIT / ASSURANCE «AYNA»
- Sorumluluk: CAPA - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K453 - AUDIT / ASSURANCE «PUSULA»
- Sorumluluk: Internal Audit - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K454 - AUDIT / ASSURANCE «ÇARK»
- Sorumluluk: Trace - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K455 - AUDIT / ASSURANCE «MÜHÜR»
- Sorumluluk: Finding - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K456 - AUDIT / ASSURANCE «ZAR»
- Sorumluluk: CAPA - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K457 - AUDIT / ASSURANCE «KANTAR»
- Sorumluluk: Internal Audit - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K458 - AUDIT / ASSURANCE «MEZİT»
- Sorumluluk: Trace - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K459 - AUDIT / ASSURANCE «ALEV»
- Sorumluluk: Finding - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K460 - AUDIT / ASSURANCE «BUZUL»
- Sorumluluk: CAPA - AUDIT / ASSURANCE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K440 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 018 - RISK MANAGEMENT «RİSK HARİTASI» K461 > K480

#### K461 - RISK MANAGEMENT «KÖPRÜ»
- Sorumluluk: Risk Register - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K462 - RISK MANAGEMENT «HAN»
- Sorumluluk: Likelihood - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K463 - RISK MANAGEMENT «DEPO»
- Sorumluluk: Mitigation - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K464 - RISK MANAGEMENT «ARENA»
- Sorumluluk: Review - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K465 - RISK MANAGEMENT «SARNIÇ»
- Sorumluluk: Risk Register - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K466 - RISK MANAGEMENT «HAT»
- Sorumluluk: Likelihood - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K467 - RISK MANAGEMENT «SIĞINAK»
- Sorumluluk: Mitigation - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K468 - RISK MANAGEMENT «MABET»
- Sorumluluk: Review - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K469 - RISK MANAGEMENT «LİMAN»
- Sorumluluk: Risk Register - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K470 - RISK MANAGEMENT «OCAK»
- Sorumluluk: Likelihood - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K471 - RISK MANAGEMENT «DİRENÇ»
- Sorumluluk: Mitigation - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K472 - RISK MANAGEMENT «PİRAMİT»
- Sorumluluk: Review - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K473 - RISK MANAGEMENT «SFENKS»
- Sorumluluk: Risk Register - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K474 - RISK MANAGEMENT «GÖVDE»
- Sorumluluk: Likelihood - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K475 - RISK MANAGEMENT «KANAL»
- Sorumluluk: Mitigation - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K476 - RISK MANAGEMENT «MHR»
- Sorumluluk: Review - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K477 - RISK MANAGEMENT «SEDEF»
- Sorumluluk: Risk Register - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K478 - RISK MANAGEMENT «HALKA»
- Sorumluluk: Likelihood - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K479 - RISK MANAGEMENT «ZİRVE»
- Sorumluluk: Mitigation - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K480 - RISK MANAGEMENT «TEMEL TAŞI»
- Sorumluluk: Review - RISK MANAGEMENT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K460 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 019 - BUSINESS CONTINUITY / DR «AFET KURTARMA SIĞINAĞI» K481 > K500

#### K481 - BUSINESS CONTINUITY / DR «DEMİRHANE»
- Sorumluluk: RTO/RPO - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K482 - BUSINESS CONTINUITY / DR «KANAT»
- Sorumluluk: Backup Drill - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K483 - BUSINESS CONTINUITY / DR «ÇEKİRDEK»
- Sorumluluk: Failover - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K484 - BUSINESS CONTINUITY / DR «MIKNATIS»
- Sorumluluk: Runbook - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K485 - BUSINESS CONTINUITY / DR «FENER»
- Sorumluluk: RTO/RPO - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K486 - BUSINESS CONTINUITY / DR «ÇELİK KAPI»
- Sorumluluk: Backup Drill - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K487 - BUSINESS CONTINUITY / DR «SUR»
- Sorumluluk: Failover - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K488 - BUSINESS CONTINUITY / DR «KULE»
- Sorumluluk: Runbook - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K489 - BUSINESS CONTINUITY / DR «HÜCRE»
- Sorumluluk: RTO/RPO - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K490 - BUSINESS CONTINUITY / DR «DÜĞÜM»
- Sorumluluk: Backup Drill - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K491 - BUSINESS CONTINUITY / DR «OMURGA»
- Sorumluluk: Failover - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K492 - BUSINESS CONTINUITY / DR «AYNA»
- Sorumluluk: Runbook - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K493 - BUSINESS CONTINUITY / DR «PUSULA»
- Sorumluluk: RTO/RPO - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K494 - BUSINESS CONTINUITY / DR «ÇARK»
- Sorumluluk: Backup Drill - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K495 - BUSINESS CONTINUITY / DR «MÜHÜR»
- Sorumluluk: Failover - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K496 - BUSINESS CONTINUITY / DR «ZAR»
- Sorumluluk: Runbook - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K497 - BUSINESS CONTINUITY / DR «KANTAR»
- Sorumluluk: RTO/RPO - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K498 - BUSINESS CONTINUITY / DR «MEZİT»
- Sorumluluk: Backup Drill - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K499 - BUSINESS CONTINUITY / DR «ALEV»
- Sorumluluk: Failover - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K500 - BUSINESS CONTINUITY / DR «BUZUL»
- Sorumluluk: Runbook - BUSINESS CONTINUITY / DR kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=ORTA · failure=fail-secure · izinli=K000-K480 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt bant tanımı · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 020 - EXTENDED ENTERPRISE - CORE «GENİŞLEME OMURGASI» K501 > K540

#### K501 - EXTENDED ENTERPRISE - CORE «KÖPRÜ»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K502 - EXTENDED ENTERPRISE - CORE «HAN»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K503 - EXTENDED ENTERPRISE - CORE «DEPO»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K504 - EXTENDED ENTERPRISE - CORE «ARENA»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K505 - EXTENDED ENTERPRISE - CORE «SARNIÇ»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K506 - EXTENDED ENTERPRISE - CORE «HAT»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K507 - EXTENDED ENTERPRISE - CORE «SIĞINAK»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K508 - EXTENDED ENTERPRISE - CORE «MABET»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K509 - EXTENDED ENTERPRISE - CORE «LİMAN»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K510 - EXTENDED ENTERPRISE - CORE «OCAK»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K511 - EXTENDED ENTERPRISE - CORE «DİRENÇ»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K512 - EXTENDED ENTERPRISE - CORE «PİRAMİT»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K513 - EXTENDED ENTERPRISE - CORE «SFENKS»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K514 - EXTENDED ENTERPRISE - CORE «GÖVDE»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K515 - EXTENDED ENTERPRISE - CORE «KANAL»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K516 - EXTENDED ENTERPRISE - CORE «MHR»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K517 - EXTENDED ENTERPRISE - CORE «SEDEF»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K518 - EXTENDED ENTERPRISE - CORE «HALKA»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K519 - EXTENDED ENTERPRISE - CORE «ZİRVE»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K520 - EXTENDED ENTERPRISE - CORE «TEMEL TAŞI»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K521 - EXTENDED ENTERPRISE - CORE «DEMİRHANE»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K522 - EXTENDED ENTERPRISE - CORE «KANAT»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K523 - EXTENDED ENTERPRISE - CORE «ÇEKİRDEK»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K524 - EXTENDED ENTERPRISE - CORE «MIKNATIS»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K525 - EXTENDED ENTERPRISE - CORE «FENER»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K526 - EXTENDED ENTERPRISE - CORE «ÇELİK KAPI»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K527 - EXTENDED ENTERPRISE - CORE «SUR»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K528 - EXTENDED ENTERPRISE - CORE «KULE»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K529 - EXTENDED ENTERPRISE - CORE «HÜCRE»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K530 - EXTENDED ENTERPRISE - CORE «DÜĞÜM»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K531 - EXTENDED ENTERPRISE - CORE «OMURGA»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K532 - EXTENDED ENTERPRISE - CORE «AYNA»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K533 - EXTENDED ENTERPRISE - CORE «PUSULA»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K534 - EXTENDED ENTERPRISE - CORE «ÇARK»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K535 - EXTENDED ENTERPRISE - CORE «MÜHÜR»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K536 - EXTENDED ENTERPRISE - CORE «ZAR»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K537 - EXTENDED ENTERPRISE - CORE «KANTAR»
- Sorumluluk: Service Catalog - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K538 - EXTENDED ENTERPRISE - CORE «MEZİT»
- Sorumluluk: Ownership - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K539 - EXTENDED ENTERPRISE - CORE «ALEV»
- Sorumluluk: SLA - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K540 - EXTENDED ENTERPRISE - CORE «BUZUL»
- Sorumluluk: SLO - EXTENDED ENTERPRISE - CORE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K500 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 021 - EXTENDED ENTERPRISE - SCALE «ÖLÇEK KANADI» K541 > K580

#### K541 - EXTENDED ENTERPRISE - SCALE «KÖPRÜ»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K542 - EXTENDED ENTERPRISE - SCALE «HAN»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K543 - EXTENDED ENTERPRISE - SCALE «DEPO»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K544 - EXTENDED ENTERPRISE - SCALE «ARENA»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K545 - EXTENDED ENTERPRISE - SCALE «SARNIÇ»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K546 - EXTENDED ENTERPRISE - SCALE «HAT»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K547 - EXTENDED ENTERPRISE - SCALE «SIĞINAK»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K548 - EXTENDED ENTERPRISE - SCALE «MABET»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K549 - EXTENDED ENTERPRISE - SCALE «LİMAN»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K550 - EXTENDED ENTERPRISE - SCALE «OCAK»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K551 - EXTENDED ENTERPRISE - SCALE «DİRENÇ»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K552 - EXTENDED ENTERPRISE - SCALE «PİRAMİT»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K553 - EXTENDED ENTERPRISE - SCALE «SFENKS»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K554 - EXTENDED ENTERPRISE - SCALE «GÖVDE»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K555 - EXTENDED ENTERPRISE - SCALE «KANAL»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K556 - EXTENDED ENTERPRISE - SCALE «MHR»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K557 - EXTENDED ENTERPRISE - SCALE «SEDEF»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K558 - EXTENDED ENTERPRISE - SCALE «HALKA»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K559 - EXTENDED ENTERPRISE - SCALE «ZİRVE»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K560 - EXTENDED ENTERPRISE - SCALE «TEMEL TAŞI»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K561 - EXTENDED ENTERPRISE - SCALE «DEMİRHANE»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K562 - EXTENDED ENTERPRISE - SCALE «KANAT»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K563 - EXTENDED ENTERPRISE - SCALE «ÇEKİRDEK»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K564 - EXTENDED ENTERPRISE - SCALE «MIKNATIS»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K565 - EXTENDED ENTERPRISE - SCALE «FENER»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K566 - EXTENDED ENTERPRISE - SCALE «ÇELİK KAPI»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K567 - EXTENDED ENTERPRISE - SCALE «SUR»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K568 - EXTENDED ENTERPRISE - SCALE «KULE»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K569 - EXTENDED ENTERPRISE - SCALE «HÜCRE»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K570 - EXTENDED ENTERPRISE - SCALE «DÜĞÜM»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K571 - EXTENDED ENTERPRISE - SCALE «OMURGA»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K572 - EXTENDED ENTERPRISE - SCALE «AYNA»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K573 - EXTENDED ENTERPRISE - SCALE «PUSULA»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K574 - EXTENDED ENTERPRISE - SCALE «ÇARK»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K575 - EXTENDED ENTERPRISE - SCALE «MÜHÜR»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K576 - EXTENDED ENTERPRISE - SCALE «ZAR»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K577 - EXTENDED ENTERPRISE - SCALE «KANTAR»
- Sorumluluk: Sharding - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K578 - EXTENDED ENTERPRISE - SCALE «MEZİT»
- Sorumluluk: Replica - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K579 - EXTENDED ENTERPRISE - SCALE «ALEV»
- Sorumluluk: Backpressure - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K580 - EXTENDED ENTERPRISE - SCALE «BUZUL»
- Sorumluluk: Autoscale - EXTENDED ENTERPRISE - SCALE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K540 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 022 - EXTENDED ENTERPRISE - DATA «VERİ OTOYOLU» K581 > K620

#### K581 - EXTENDED ENTERPRISE - DATA «KÖPRÜ»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K582 - EXTENDED ENTERPRISE - DATA «HAN»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K583 - EXTENDED ENTERPRISE - DATA «DEPO»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K584 - EXTENDED ENTERPRISE - DATA «ARENA»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K585 - EXTENDED ENTERPRISE - DATA «SARNIÇ»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K586 - EXTENDED ENTERPRISE - DATA «HAT»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K587 - EXTENDED ENTERPRISE - DATA «SIĞINAK»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K588 - EXTENDED ENTERPRISE - DATA «MABET»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K589 - EXTENDED ENTERPRISE - DATA «LİMAN»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K590 - EXTENDED ENTERPRISE - DATA «OCAK»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K591 - EXTENDED ENTERPRISE - DATA «DİRENÇ»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K592 - EXTENDED ENTERPRISE - DATA «PİRAMİT»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K593 - EXTENDED ENTERPRISE - DATA «SFENKS»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K594 - EXTENDED ENTERPRISE - DATA «GÖVDE»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K595 - EXTENDED ENTERPRISE - DATA «KANAL»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K596 - EXTENDED ENTERPRISE - DATA «MHR»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K597 - EXTENDED ENTERPRISE - DATA «SEDEF»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K598 - EXTENDED ENTERPRISE - DATA «HALKA»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K599 - EXTENDED ENTERPRISE - DATA «ZİRVE»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K600 - EXTENDED ENTERPRISE - DATA «TEMEL TAŞI»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K601 - EXTENDED ENTERPRISE - DATA «DEMİRHANE»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K602 - EXTENDED ENTERPRISE - DATA «KANAT»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K603 - EXTENDED ENTERPRISE - DATA «ÇEKİRDEK»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K604 - EXTENDED ENTERPRISE - DATA «MIKNATIS»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K605 - EXTENDED ENTERPRISE - DATA «FENER»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K606 - EXTENDED ENTERPRISE - DATA «ÇELİK KAPI»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K607 - EXTENDED ENTERPRISE - DATA «SUR»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K608 - EXTENDED ENTERPRISE - DATA «KULE»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K609 - EXTENDED ENTERPRISE - DATA «HÜCRE»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K610 - EXTENDED ENTERPRISE - DATA «DÜĞÜM»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K611 - EXTENDED ENTERPRISE - DATA «OMURGA»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K612 - EXTENDED ENTERPRISE - DATA «AYNA»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K613 - EXTENDED ENTERPRISE - DATA «PUSULA»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K614 - EXTENDED ENTERPRISE - DATA «ÇARK»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K615 - EXTENDED ENTERPRISE - DATA «MÜHÜR»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K616 - EXTENDED ENTERPRISE - DATA «ZAR»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K617 - EXTENDED ENTERPRISE - DATA «KANTAR»
- Sorumluluk: Pipeline - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K618 - EXTENDED ENTERPRISE - DATA «MEZİT»
- Sorumluluk: Lineage - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K619 - EXTENDED ENTERPRISE - DATA «ALEV»
- Sorumluluk: Schema Registry - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K620 - EXTENDED ENTERPRISE - DATA «BUZUL»
- Sorumluluk: Quality Gate - EXTENDED ENTERPRISE - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K580 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 023 - EXTENDED ENTERPRISE - OPS «OPERASYON KÖPRÜSU» K621 > K660

#### K621 - EXTENDED ENTERPRISE - OPS «KÖPRÜ»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K622 - EXTENDED ENTERPRISE - OPS «HAN»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K623 - EXTENDED ENTERPRISE - OPS «DEPO»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K624 - EXTENDED ENTERPRISE - OPS «ARENA»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K625 - EXTENDED ENTERPRISE - OPS «SARNIÇ»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K626 - EXTENDED ENTERPRISE - OPS «HAT»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K627 - EXTENDED ENTERPRISE - OPS «SIĞINAK»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K628 - EXTENDED ENTERPRISE - OPS «MABET»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K629 - EXTENDED ENTERPRISE - OPS «LİMAN»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K630 - EXTENDED ENTERPRISE - OPS «OCAK»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K631 - EXTENDED ENTERPRISE - OPS «DİRENÇ»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K632 - EXTENDED ENTERPRISE - OPS «PİRAMİT»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K633 - EXTENDED ENTERPRISE - OPS «SFENKS»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K634 - EXTENDED ENTERPRISE - OPS «GÖVDE»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K635 - EXTENDED ENTERPRISE - OPS «KANAL»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K636 - EXTENDED ENTERPRISE - OPS «MHR»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K637 - EXTENDED ENTERPRISE - OPS «SEDEF»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K638 - EXTENDED ENTERPRISE - OPS «HALKA»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K639 - EXTENDED ENTERPRISE - OPS «ZİRVE»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K640 - EXTENDED ENTERPRISE - OPS «TEMEL TAŞI»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K641 - EXTENDED ENTERPRISE - OPS «DEMİRHANE»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K642 - EXTENDED ENTERPRISE - OPS «KANAT»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K643 - EXTENDED ENTERPRISE - OPS «ÇEKİRDEK»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K644 - EXTENDED ENTERPRISE - OPS «MIKNATIS»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K645 - EXTENDED ENTERPRISE - OPS «FENER»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K646 - EXTENDED ENTERPRISE - OPS «ÇELİK KAPI»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K647 - EXTENDED ENTERPRISE - OPS «SUR»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K648 - EXTENDED ENTERPRISE - OPS «KULE»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K649 - EXTENDED ENTERPRISE - OPS «HÜCRE»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K650 - EXTENDED ENTERPRISE - OPS «DÜĞÜM»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K651 - EXTENDED ENTERPRISE - OPS «OMURGA»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K652 - EXTENDED ENTERPRISE - OPS «AYNA»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K653 - EXTENDED ENTERPRISE - OPS «PUSULA»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K654 - EXTENDED ENTERPRISE - OPS «ÇARK»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K655 - EXTENDED ENTERPRISE - OPS «MÜHÜR»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K656 - EXTENDED ENTERPRISE - OPS «ZAR»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K657 - EXTENDED ENTERPRISE - OPS «KANTAR»
- Sorumluluk: Runbook - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K658 - EXTENDED ENTERPRISE - OPS «MEZİT»
- Sorumluluk: On-Call - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K659 - EXTENDED ENTERPRISE - OPS «ALEV»
- Sorumluluk: Incident - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K660 - EXTENDED ENTERPRISE - OPS «BUZUL»
- Sorumluluk: Postmortem - EXTENDED ENTERPRISE - OPS kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K620 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 024 - EXTENDED ENTERPRISE - PRODUCT «ÜRÜN HATTI» K661 > K700

#### K661 - EXTENDED ENTERPRISE - PRODUCT «KÖPRÜ»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K662 - EXTENDED ENTERPRISE - PRODUCT «HAN»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K663 - EXTENDED ENTERPRISE - PRODUCT «DEPO»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K664 - EXTENDED ENTERPRISE - PRODUCT «ARENA»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K665 - EXTENDED ENTERPRISE - PRODUCT «SARNIÇ»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K666 - EXTENDED ENTERPRISE - PRODUCT «HAT»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K667 - EXTENDED ENTERPRISE - PRODUCT «SIĞINAK»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K668 - EXTENDED ENTERPRISE - PRODUCT «MABET»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K669 - EXTENDED ENTERPRISE - PRODUCT «LİMAN»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K670 - EXTENDED ENTERPRISE - PRODUCT «OCAK»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K671 - EXTENDED ENTERPRISE - PRODUCT «DİRENÇ»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K672 - EXTENDED ENTERPRISE - PRODUCT «PİRAMİT»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K673 - EXTENDED ENTERPRISE - PRODUCT «SFENKS»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K674 - EXTENDED ENTERPRISE - PRODUCT «GÖVDE»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K675 - EXTENDED ENTERPRISE - PRODUCT «KANAL»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K676 - EXTENDED ENTERPRISE - PRODUCT «MHR»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K677 - EXTENDED ENTERPRISE - PRODUCT «SEDEF»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K678 - EXTENDED ENTERPRISE - PRODUCT «HALKA»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K679 - EXTENDED ENTERPRISE - PRODUCT «ZİRVE»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K680 - EXTENDED ENTERPRISE - PRODUCT «TEMEL TAŞI»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K681 - EXTENDED ENTERPRISE - PRODUCT «DEMİRHANE»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K682 - EXTENDED ENTERPRISE - PRODUCT «KANAT»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K683 - EXTENDED ENTERPRISE - PRODUCT «ÇEKİRDEK»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K684 - EXTENDED ENTERPRISE - PRODUCT «MIKNATIS»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K685 - EXTENDED ENTERPRISE - PRODUCT «FENER»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K686 - EXTENDED ENTERPRISE - PRODUCT «ÇELİK KAPI»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K687 - EXTENDED ENTERPRISE - PRODUCT «SUR»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K688 - EXTENDED ENTERPRISE - PRODUCT «KULE»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K689 - EXTENDED ENTERPRISE - PRODUCT «HÜCRE»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K690 - EXTENDED ENTERPRISE - PRODUCT «DÜĞÜM»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K691 - EXTENDED ENTERPRISE - PRODUCT «OMURGA»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K692 - EXTENDED ENTERPRISE - PRODUCT «AYNA»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K693 - EXTENDED ENTERPRISE - PRODUCT «PUSULA»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K694 - EXTENDED ENTERPRISE - PRODUCT «ÇARK»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K695 - EXTENDED ENTERPRISE - PRODUCT «MÜHÜR»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K696 - EXTENDED ENTERPRISE - PRODUCT «ZAR»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K697 - EXTENDED ENTERPRISE - PRODUCT «KANTAR»
- Sorumluluk: Roadmap - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K698 - EXTENDED ENTERPRISE - PRODUCT «MEZİT»
- Sorumluluk: Backlog - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K699 - EXTENDED ENTERPRISE - PRODUCT «ALEV»
- Sorumluluk: Acceptance - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K700 - EXTENDED ENTERPRISE - PRODUCT «BUZUL»
- Sorumluluk: Release Note - EXTENDED ENTERPRISE - PRODUCT kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K660 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 025 - EXTENDED ENTERPRISE - GROWTH «BÜYÜME MOTORU» K701 > K740

#### K701 - EXTENDED ENTERPRISE - GROWTH «KÖPRÜ»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K702 - EXTENDED ENTERPRISE - GROWTH «HAN»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K703 - EXTENDED ENTERPRISE - GROWTH «DEPO»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K704 - EXTENDED ENTERPRISE - GROWTH «ARENA»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K705 - EXTENDED ENTERPRISE - GROWTH «SARNIÇ»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K706 - EXTENDED ENTERPRISE - GROWTH «HAT»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K707 - EXTENDED ENTERPRISE - GROWTH «SIĞINAK»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K708 - EXTENDED ENTERPRISE - GROWTH «MABET»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K709 - EXTENDED ENTERPRISE - GROWTH «LİMAN»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K710 - EXTENDED ENTERPRISE - GROWTH «OCAK»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K711 - EXTENDED ENTERPRISE - GROWTH «DİRENÇ»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K712 - EXTENDED ENTERPRISE - GROWTH «PİRAMİT»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K713 - EXTENDED ENTERPRISE - GROWTH «SFENKS»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K714 - EXTENDED ENTERPRISE - GROWTH «GÖVDE»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K715 - EXTENDED ENTERPRISE - GROWTH «KANAL»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K716 - EXTENDED ENTERPRISE - GROWTH «MHR»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K717 - EXTENDED ENTERPRISE - GROWTH «SEDEF»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K718 - EXTENDED ENTERPRISE - GROWTH «HALKA»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K719 - EXTENDED ENTERPRISE - GROWTH «ZİRVE»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K720 - EXTENDED ENTERPRISE - GROWTH «TEMEL TAŞI»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K721 - EXTENDED ENTERPRISE - GROWTH «DEMİRHANE»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K722 - EXTENDED ENTERPRISE - GROWTH «KANAT»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K723 - EXTENDED ENTERPRISE - GROWTH «ÇEKİRDEK»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K724 - EXTENDED ENTERPRISE - GROWTH «MIKNATIS»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K725 - EXTENDED ENTERPRISE - GROWTH «FENER»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K726 - EXTENDED ENTERPRISE - GROWTH «ÇELİK KAPI»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K727 - EXTENDED ENTERPRISE - GROWTH «SUR»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K728 - EXTENDED ENTERPRISE - GROWTH «KULE»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K729 - EXTENDED ENTERPRISE - GROWTH «HÜCRE»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K730 - EXTENDED ENTERPRISE - GROWTH «DÜĞÜM»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K731 - EXTENDED ENTERPRISE - GROWTH «OMURGA»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K732 - EXTENDED ENTERPRISE - GROWTH «AYNA»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K733 - EXTENDED ENTERPRISE - GROWTH «PUSULA»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K734 - EXTENDED ENTERPRISE - GROWTH «ÇARK»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K735 - EXTENDED ENTERPRISE - GROWTH «MÜHÜR»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K736 - EXTENDED ENTERPRISE - GROWTH «ZAR»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K737 - EXTENDED ENTERPRISE - GROWTH «KANTAR»
- Sorumluluk: Funnel - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K738 - EXTENDED ENTERPRISE - GROWTH «MEZİT»
- Sorumluluk: Retention - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K739 - EXTENDED ENTERPRISE - GROWTH «ALEV»
- Sorumluluk: Experiment - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K740 - EXTENDED ENTERPRISE - GROWTH «BUZUL»
- Sorumluluk: Cohort - EXTENDED ENTERPRISE - GROWTH kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K700 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 026 - EXTENDED ENTERPRISE - TRUST «GÜVEN HATTI» K741 > K780

#### K741 - EXTENDED ENTERPRISE - TRUST «KÖPRÜ»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K742 - EXTENDED ENTERPRISE - TRUST «HAN»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K743 - EXTENDED ENTERPRISE - TRUST «DEPO»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K744 - EXTENDED ENTERPRISE - TRUST «ARENA»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K745 - EXTENDED ENTERPRISE - TRUST «SARNIÇ»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K746 - EXTENDED ENTERPRISE - TRUST «HAT»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K747 - EXTENDED ENTERPRISE - TRUST «SIĞINAK»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K748 - EXTENDED ENTERPRISE - TRUST «MABET»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K749 - EXTENDED ENTERPRISE - TRUST «LİMAN»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K750 - EXTENDED ENTERPRISE - TRUST «OCAK»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K751 - EXTENDED ENTERPRISE - TRUST «DİRENÇ»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K752 - EXTENDED ENTERPRISE - TRUST «PİRAMİT»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K753 - EXTENDED ENTERPRISE - TRUST «SFENKS»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K754 - EXTENDED ENTERPRISE - TRUST «GÖVDE»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K755 - EXTENDED ENTERPRISE - TRUST «KANAL»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K756 - EXTENDED ENTERPRISE - TRUST «MHR»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K757 - EXTENDED ENTERPRISE - TRUST «SEDEF»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K758 - EXTENDED ENTERPRISE - TRUST «HALKA»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K759 - EXTENDED ENTERPRISE - TRUST «ZİRVE»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K760 - EXTENDED ENTERPRISE - TRUST «TEMEL TAŞI»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K761 - EXTENDED ENTERPRISE - TRUST «DEMİRHANE»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K762 - EXTENDED ENTERPRISE - TRUST «KANAT»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K763 - EXTENDED ENTERPRISE - TRUST «ÇEKİRDEK»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K764 - EXTENDED ENTERPRISE - TRUST «MIKNATIS»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K765 - EXTENDED ENTERPRISE - TRUST «FENER»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K766 - EXTENDED ENTERPRISE - TRUST «ÇELİK KAPI»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K767 - EXTENDED ENTERPRISE - TRUST «SUR»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K768 - EXTENDED ENTERPRISE - TRUST «KULE»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K769 - EXTENDED ENTERPRISE - TRUST «HÜCRE»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K770 - EXTENDED ENTERPRISE - TRUST «DÜĞÜM»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K771 - EXTENDED ENTERPRISE - TRUST «OMURGA»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K772 - EXTENDED ENTERPRISE - TRUST «AYNA»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K773 - EXTENDED ENTERPRISE - TRUST «PUSULA»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K774 - EXTENDED ENTERPRISE - TRUST «ÇARK»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K775 - EXTENDED ENTERPRISE - TRUST «MÜHÜR»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K776 - EXTENDED ENTERPRISE - TRUST «ZAR»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K777 - EXTENDED ENTERPRISE - TRUST «KANTAR»
- Sorumluluk: Trust Center - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K778 - EXTENDED ENTERPRISE - TRUST «MEZİT»
- Sorumluluk: DPA - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K779 - EXTENDED ENTERPRISE - TRUST «ALEV»
- Sorumluluk: Uptime - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K780 - EXTENDED ENTERPRISE - TRUST «BUZUL»
- Sorumluluk: Status - EXTENDED ENTERPRISE - TRUST kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K740 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 027 - DEEP PLATFORM - RUNTIME «ÇALIŞMA ZAMANI PIYESI» K781 > K820

#### K781 - DEEP PLATFORM - RUNTIME «KÖPRÜ»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K782 - DEEP PLATFORM - RUNTIME «HAN»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K783 - DEEP PLATFORM - RUNTIME «DEPO»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K784 - DEEP PLATFORM - RUNTIME «ARENA»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K785 - DEEP PLATFORM - RUNTIME «SARNIÇ»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K786 - DEEP PLATFORM - RUNTIME «HAT»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K787 - DEEP PLATFORM - RUNTIME «SIĞINAK»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K788 - DEEP PLATFORM - RUNTIME «MABET»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K789 - DEEP PLATFORM - RUNTIME «LİMAN»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K790 - DEEP PLATFORM - RUNTIME «OCAK»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K791 - DEEP PLATFORM - RUNTIME «DİRENÇ»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K792 - DEEP PLATFORM - RUNTIME «PİRAMİT»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K793 - DEEP PLATFORM - RUNTIME «SFENKS»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K794 - DEEP PLATFORM - RUNTIME «GÖVDE»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K795 - DEEP PLATFORM - RUNTIME «KANAL»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K796 - DEEP PLATFORM - RUNTIME «MHR»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K797 - DEEP PLATFORM - RUNTIME «SEDEF»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K798 - DEEP PLATFORM - RUNTIME «HALKA»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K799 - DEEP PLATFORM - RUNTIME «ZİRVE»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K800 - DEEP PLATFORM - RUNTIME «TEMEL TAŞI»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K801 - DEEP PLATFORM - RUNTIME «DEMİRHANE»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K802 - DEEP PLATFORM - RUNTIME «KANAT»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K803 - DEEP PLATFORM - RUNTIME «ÇEKİRDEK»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K804 - DEEP PLATFORM - RUNTIME «MIKNATIS»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K805 - DEEP PLATFORM - RUNTIME «FENER»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K806 - DEEP PLATFORM - RUNTIME «ÇELİK KAPI»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K807 - DEEP PLATFORM - RUNTIME «SUR»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K808 - DEEP PLATFORM - RUNTIME «KULE»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K809 - DEEP PLATFORM - RUNTIME «HÜCRE»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K810 - DEEP PLATFORM - RUNTIME «DÜĞÜM»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K811 - DEEP PLATFORM - RUNTIME «OMURGA»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K812 - DEEP PLATFORM - RUNTIME «AYNA»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K813 - DEEP PLATFORM - RUNTIME «PUSULA»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K814 - DEEP PLATFORM - RUNTIME «ÇARK»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K815 - DEEP PLATFORM - RUNTIME «MÜHÜR»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K816 - DEEP PLATFORM - RUNTIME «ZAR»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K817 - DEEP PLATFORM - RUNTIME «KANTAR»
- Sorumluluk: Scheduler - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K818 - DEEP PLATFORM - RUNTIME «MEZİT»
- Sorumluluk: Loop - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K819 - DEEP PLATFORM - RUNTIME «ALEV»
- Sorumluluk: Isolation - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K820 - DEEP PLATFORM - RUNTIME «BUZUL»
- Sorumluluk: Backpressure - DEEP PLATFORM - RUNTIME kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K780 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 028 - DEEP PLATFORM - DATA «VERİ SAHNESİ» K821 > K860

#### K821 - DEEP PLATFORM - DATA «KÖPRÜ»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K822 - DEEP PLATFORM - DATA «HAN»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K823 - DEEP PLATFORM - DATA «DEPO»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K824 - DEEP PLATFORM - DATA «ARENA»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K825 - DEEP PLATFORM - DATA «SARNIÇ»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K826 - DEEP PLATFORM - DATA «HAT»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K827 - DEEP PLATFORM - DATA «SIĞINAK»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K828 - DEEP PLATFORM - DATA «MABET»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K829 - DEEP PLATFORM - DATA «LİMAN»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K830 - DEEP PLATFORM - DATA «OCAK»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K831 - DEEP PLATFORM - DATA «DİRENÇ»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K832 - DEEP PLATFORM - DATA «PİRAMİT»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K833 - DEEP PLATFORM - DATA «SFENKS»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K834 - DEEP PLATFORM - DATA «GÖVDE»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K835 - DEEP PLATFORM - DATA «KANAL»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K836 - DEEP PLATFORM - DATA «MHR»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K837 - DEEP PLATFORM - DATA «SEDEF»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K838 - DEEP PLATFORM - DATA «HALKA»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K839 - DEEP PLATFORM - DATA «ZİRVE»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K840 - DEEP PLATFORM - DATA «TEMEL TAŞI»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K841 - DEEP PLATFORM - DATA «DEMİRHANE»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K842 - DEEP PLATFORM - DATA «KANAT»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K843 - DEEP PLATFORM - DATA «ÇEKİRDEK»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K844 - DEEP PLATFORM - DATA «MIKNATIS»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K845 - DEEP PLATFORM - DATA «FENER»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K846 - DEEP PLATFORM - DATA «ÇELİK KAPI»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K847 - DEEP PLATFORM - DATA «SUR»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K848 - DEEP PLATFORM - DATA «KULE»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K849 - DEEP PLATFORM - DATA «HÜCRE»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K850 - DEEP PLATFORM - DATA «DÜĞÜM»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K851 - DEEP PLATFORM - DATA «OMURGA»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K852 - DEEP PLATFORM - DATA «AYNA»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K853 - DEEP PLATFORM - DATA «PUSULA»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K854 - DEEP PLATFORM - DATA «ÇARK»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K855 - DEEP PLATFORM - DATA «MÜHÜR»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K856 - DEEP PLATFORM - DATA «ZAR»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K857 - DEEP PLATFORM - DATA «KANTAR»
- Sorumluluk: Partition - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K858 - DEEP PLATFORM - DATA «MEZİT»
- Sorumluluk: Index - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K859 - DEEP PLATFORM - DATA «ALEV»
- Sorumluluk: Cache Tier - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K860 - DEEP PLATFORM - DATA «BUZUL»
- Sorumluluk: Compaction - DEEP PLATFORM - DATA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K820 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 029 - DEEP PLATFORM - AI «ZEKA SAHNESI» K861 > K900

#### K861 - DEEP PLATFORM - AI «KÖPRÜ»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K862 - DEEP PLATFORM - AI «HAN»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K863 - DEEP PLATFORM - AI «DEPO»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K864 - DEEP PLATFORM - AI «ARENA»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K865 - DEEP PLATFORM - AI «SARNIÇ»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K866 - DEEP PLATFORM - AI «HAT»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K867 - DEEP PLATFORM - AI «SIĞINAK»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K868 - DEEP PLATFORM - AI «MABET»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K869 - DEEP PLATFORM - AI «LİMAN»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K870 - DEEP PLATFORM - AI «OCAK»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K871 - DEEP PLATFORM - AI «DİRENÇ»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K872 - DEEP PLATFORM - AI «PİRAMİT»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K873 - DEEP PLATFORM - AI «SFENKS»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K874 - DEEP PLATFORM - AI «GÖVDE»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K875 - DEEP PLATFORM - AI «KANAL»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K876 - DEEP PLATFORM - AI «MHR»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K877 - DEEP PLATFORM - AI «SEDEF»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K878 - DEEP PLATFORM - AI «HALKA»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K879 - DEEP PLATFORM - AI «ZİRVE»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K880 - DEEP PLATFORM - AI «TEMEL TAŞI»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K881 - DEEP PLATFORM - AI «DEMİRHANE»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K882 - DEEP PLATFORM - AI «KANAT»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K883 - DEEP PLATFORM - AI «ÇEKİRDEK»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K884 - DEEP PLATFORM - AI «MIKNATIS»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K885 - DEEP PLATFORM - AI «FENER»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K886 - DEEP PLATFORM - AI «ÇELİK KAPI»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K887 - DEEP PLATFORM - AI «SUR»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K888 - DEEP PLATFORM - AI «KULE»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K889 - DEEP PLATFORM - AI «HÜCRE»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K890 - DEEP PLATFORM - AI «DÜĞÜM»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K891 - DEEP PLATFORM - AI «OMURGA»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K892 - DEEP PLATFORM - AI «AYNA»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K893 - DEEP PLATFORM - AI «PUSULA»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K894 - DEEP PLATFORM - AI «ÇARK»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K895 - DEEP PLATFORM - AI «MÜHÜR»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K896 - DEEP PLATFORM - AI «ZAR»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K897 - DEEP PLATFORM - AI «KANTAR»
- Sorumluluk: Feature Store - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K898 - DEEP PLATFORM - AI «MEZİT»
- Sorumluluk: Training - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K899 - DEEP PLATFORM - AI «ALEV»
- Sorumluluk: Serving - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K900 - DEEP PLATFORM - AI «BUZUL»
- Sorumluluk: Monitor - DEEP PLATFORM - AI kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K860 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 030 - DEEP PLATFORM - MEDIA «MEDYA SAHNESI» K901 > K940

#### K901 - DEEP PLATFORM - MEDIA «KÖPRÜ»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K902 - DEEP PLATFORM - MEDIA «HAN»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K903 - DEEP PLATFORM - MEDIA «DEPO»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K904 - DEEP PLATFORM - MEDIA «ARENA»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K905 - DEEP PLATFORM - MEDIA «SARNIÇ»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K906 - DEEP PLATFORM - MEDIA «HAT»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K907 - DEEP PLATFORM - MEDIA «SIĞINAK»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K908 - DEEP PLATFORM - MEDIA «MABET»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K909 - DEEP PLATFORM - MEDIA «LİMAN»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K910 - DEEP PLATFORM - MEDIA «OCAK»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K911 - DEEP PLATFORM - MEDIA «DİRENÇ»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K912 - DEEP PLATFORM - MEDIA «PİRAMİT»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K913 - DEEP PLATFORM - MEDIA «SFENKS»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K914 - DEEP PLATFORM - MEDIA «GÖVDE»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K915 - DEEP PLATFORM - MEDIA «KANAL»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K916 - DEEP PLATFORM - MEDIA «MHR»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K917 - DEEP PLATFORM - MEDIA «SEDEF»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K918 - DEEP PLATFORM - MEDIA «HALKA»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K919 - DEEP PLATFORM - MEDIA «ZİRVE»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K920 - DEEP PLATFORM - MEDIA «TEMEL TAŞI»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K921 - DEEP PLATFORM - MEDIA «DEMİRHANE»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K922 - DEEP PLATFORM - MEDIA «KANAT»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K923 - DEEP PLATFORM - MEDIA «ÇEKİRDEK»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K924 - DEEP PLATFORM - MEDIA «MIKNATIS»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K925 - DEEP PLATFORM - MEDIA «FENER»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K926 - DEEP PLATFORM - MEDIA «ÇELİK KAPI»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K927 - DEEP PLATFORM - MEDIA «SUR»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K928 - DEEP PLATFORM - MEDIA «KULE»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K929 - DEEP PLATFORM - MEDIA «HÜCRE»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K930 - DEEP PLATFORM - MEDIA «DÜĞÜM»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K931 - DEEP PLATFORM - MEDIA «OMURGA»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K932 - DEEP PLATFORM - MEDIA «AYNA»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K933 - DEEP PLATFORM - MEDIA «PUSULA»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K934 - DEEP PLATFORM - MEDIA «ÇARK»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K935 - DEEP PLATFORM - MEDIA «MÜHÜR»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K936 - DEEP PLATFORM - MEDIA «ZAR»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K937 - DEEP PLATFORM - MEDIA «KANTAR»
- Sorumluluk: Transcode - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K938 - DEEP PLATFORM - MEDIA «MEZİT»
- Sorumluluk: Packager - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K939 - DEEP PLATFORM - MEDIA «ALEV»
- Sorumluluk: Manifest - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K940 - DEEP PLATFORM - MEDIA «BUZUL»
- Sorumluluk: Watermark - DEEP PLATFORM - MEDIA kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K900 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 031 - DEEP PLATFORM - EDGE «KENAR SAHNESİ» K941 > K980

#### K941 - DEEP PLATFORM - EDGE «KÖPRÜ»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K942 - DEEP PLATFORM - EDGE «HAN»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K943 - DEEP PLATFORM - EDGE «DEPO»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K944 - DEEP PLATFORM - EDGE «ARENA»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K945 - DEEP PLATFORM - EDGE «SARNIÇ»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K946 - DEEP PLATFORM - EDGE «HAT»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K947 - DEEP PLATFORM - EDGE «SIĞINAK»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K948 - DEEP PLATFORM - EDGE «MABET»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K949 - DEEP PLATFORM - EDGE «LİMAN»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K950 - DEEP PLATFORM - EDGE «OCAK»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K951 - DEEP PLATFORM - EDGE «DİRENÇ»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K952 - DEEP PLATFORM - EDGE «PİRAMİT»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K953 - DEEP PLATFORM - EDGE «SFENKS»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K954 - DEEP PLATFORM - EDGE «GÖVDE»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K955 - DEEP PLATFORM - EDGE «KANAL»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K956 - DEEP PLATFORM - EDGE «MHR»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K957 - DEEP PLATFORM - EDGE «SEDEF»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K958 - DEEP PLATFORM - EDGE «HALKA»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K959 - DEEP PLATFORM - EDGE «ZİRVE»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K960 - DEEP PLATFORM - EDGE «TEMEL TAŞI»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K961 - DEEP PLATFORM - EDGE «DEMİRHANE»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K962 - DEEP PLATFORM - EDGE «KANAT»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K963 - DEEP PLATFORM - EDGE «ÇEKİRDEK»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K964 - DEEP PLATFORM - EDGE «MIKNATIS»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K965 - DEEP PLATFORM - EDGE «FENER»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K966 - DEEP PLATFORM - EDGE «ÇELİK KAPI»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K967 - DEEP PLATFORM - EDGE «SUR»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K968 - DEEP PLATFORM - EDGE «KULE»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K969 - DEEP PLATFORM - EDGE «HÜCRE»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K970 - DEEP PLATFORM - EDGE «DÜĞÜM»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K971 - DEEP PLATFORM - EDGE «OMURGA»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K972 - DEEP PLATFORM - EDGE «AYNA»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K973 - DEEP PLATFORM - EDGE «PUSULA»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K974 - DEEP PLATFORM - EDGE «ÇARK»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K975 - DEEP PLATFORM - EDGE «MÜHÜR»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K976 - DEEP PLATFORM - EDGE «ZAR»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K977 - DEEP PLATFORM - EDGE «KANTAR»
- Sorumluluk: Edge Worker - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K978 - DEEP PLATFORM - EDGE «MEZİT»
- Sorumluluk: PoP - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K979 - DEEP PLATFORM - EDGE «ALEV»
- Sorumluluk: Cache - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K980 - DEEP PLATFORM - EDGE «BUZUL»
- Sorumluluk: Origin - DEEP PLATFORM - EDGE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K940 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 032 - DEEP DOMAIN - AUTOMOTIVE «OTOMOTİV DERİNLİĞİ» K981 > K1020

#### K981 - DEEP DOMAIN - AUTOMOTIVE «KÖPRÜ»
- Sorumluluk: Seat Audio - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K982 - DEEP DOMAIN - AUTOMOTIVE «HAN»
- Sorumluluk: ANC - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K983 - DEEP DOMAIN - AUTOMOTIVE «DEPO»
- Sorumluluk: Zoning - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K984 - DEEP DOMAIN - AUTOMOTIVE «ARENA»
- Sorumluluk: Motor Noise - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K985 - DEEP DOMAIN - AUTOMOTIVE «SARNIÇ»
- Sorumluluk: Seat Audio - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K986 - DEEP DOMAIN - AUTOMOTIVE «HAT»
- Sorumluluk: ANC - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K987 - DEEP DOMAIN - AUTOMOTIVE «SIĞINAK»
- Sorumluluk: Zoning - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K988 - DEEP DOMAIN - AUTOMOTIVE «MABET»
- Sorumluluk: Motor Noise - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K989 - DEEP DOMAIN - AUTOMOTIVE «LİMAN»
- Sorumluluk: Seat Audio - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K990 - DEEP DOMAIN - AUTOMOTIVE «OCAK»
- Sorumluluk: ANC - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K991 - DEEP DOMAIN - AUTOMOTIVE «DİRENÇ»
- Sorumluluk: Zoning - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K992 - DEEP DOMAIN - AUTOMOTIVE «PİRAMİT»
- Sorumluluk: Motor Noise - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K993 - DEEP DOMAIN - AUTOMOTIVE «SFENKS»
- Sorumluluk: Seat Audio - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K994 - DEEP DOMAIN - AUTOMOTIVE «GÖVDE»
- Sorumluluk: ANC - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K995 - DEEP DOMAIN - AUTOMOTIVE «KANAL»
- Sorumluluk: Zoning - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 5/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K996 - DEEP DOMAIN - AUTOMOTIVE «MHR»
- Sorumluluk: Motor Noise - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 1/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K997 - DEEP DOMAIN - AUTOMOTIVE «SEDEF»
- Sorumluluk: Seat Audio - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 2/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K998 - DEEP DOMAIN - AUTOMOTIVE «HALKA»
- Sorumluluk: ANC - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 3/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

#### K999 - DEEP DOMAIN - AUTOMOTIVE «ZİRVE»
- Sorumluluk: Zoning - DEEP DOMAIN - AUTOMOTIVE kapsamının bu katmanın payı (derinlik kademesi 4/5).
- Sınır: data=band veri sınırı · security=YÜKSEK · failure=fail-secure · izinli=K000-K980 alt katmanları · yasak=üst bantlara doğrudan çağrı (yalnız port/adapter)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web) · decomposition: K→Domain→Subdomain→BC→Aggregate→Module→Component→Service→Adapter→Impl

### BANT 033 - DEEP DOMAIN - HOME «EV DERİNLİĞİ» K1021 > K1060

- Bant kısayolu: K1021 > K1060 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Room EQ · Voice Zone · Scene Graph
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 034 - DEEP DOMAIN - STUDIO «STÜDYO DERİNLİĞİ» K1061 > K1100

- Bant kısayolu: K1061 > K1100 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Renderer · Plugin Host · Control Surface
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 035 - DEEP DOMAIN - RIGHTS «HAK DERİNLİĞİ» K1101 > K1140

- Bant kısayolu: K1101 > K1140 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Territory · Royalty · Split · Claim
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 036 - DEEP DOMAIN - COMMERCE «TİCARET DERİNLİĞİ» K1141 > K1180

- Bant kısayolu: K1141 > K1180 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Tax · Invoice · Payout · Chargeback
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 037 - RUNTIME - WEB «WEB ÇALIŞMA ZAMANI» K1181 > K1220

- Bant kısayolu: K1181 > K1220 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Router · State · Render · Worker
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 038 - RUNTIME - SERVER «SUNUCU ÇALIŞMA ZAMANI» K1221 > K1260

- Bant kısayolu: K1221 > K1260 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Pipeline · Handler · Service · Repository
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 039 - RUNTIME - DESKTOP «MASAÜSTÜ ÇALIŞMA ZAMANI» K1261 > K1300

- Bant kısayolu: K1261 > K1300 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Electron Main · IPC · Tray · Auto-Update
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 040 - RUNTIME - EMBEDDED «GÖMÜLÜ ÇALIŞMA ZAMANI» K1301 > K1340

- Bant kısayolu: K1301 > K1340 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Boot · RTOS Task · Interrupt · DMA
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 041 - RUNTIME - DSP «DSP ÇALIŞMA ZAMANI» K1341 > K1380

- Bant kısayolu: K1341 > K1380 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Block Size · Graph · Sched · Latency
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 042 - CROSS-CUTTING - SECURITY «GÜVENLİK KATMANI» K1381 > K1420

- Bant kısayolu: K1381 > K1420 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Threat Model · Crypto · AuthN/Z · Audit
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 043 - CROSS-CUTTING - OBSERVABILITY «GÖZLEM KATMANI» K1421 > K1460

- Bant kısayolu: K1421 > K1460 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Log · Metric · Trace · Alert
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 044 - CROSS-CUTTING - DELIVERY «TESLİMAT KATMANI» K1461 > K1500

- Bant kısayolu: K1461 > K1500 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Build · Test Gate · Deploy · Rollback
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 045 - CROSS-CUTTING - DATA GOV. «VERİ YÖNETİMİ» K1501 > K1540

- Bant kısayolu: K1501 > K1540 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Catalog · PII Class · Retention · Masking
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 046 - CROSS-CUTTING - COST «MALİYET KATMANI» K1541 > K1580

- Bant kısayolu: K1541 > K1580 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Budget · Tagging · Rightsizing · Forecast
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 047 - KNOWLEDGE - GRAPH DEEP «BİLGİ GRAFİĞİ DERİNLİĞİ» K1581 > K1620

- Bant kısayolu: K1581 > K1620 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Ontology · Link · Reasoning · Query
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 048 - KNOWLEDGE - RAG DEEP «RAG DERİNLİĞİ» K1621 > K1660

- Bant kısayolu: K1621 > K1660 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Chunker · Reranker · Citation · Eval
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 049 - KNOWLEDGE - AGENT DEEP «AJAN DERİNLİĞİ» K1661 > K1700

- Bant kısayolu: K1661 > K1700 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Planner · Tool Loop · Memory · Guard
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 050 - SEARCH - SEMANTIK «ANLAMSAL ARAMA» K1701 > K1740

- Bant kısayolu: K1701 > K1740 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Embedding · HNSW · Hybrid · Snippet
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 051 - SEARCH - KURAL «KURAL ARAMA» K1741 > K1780

- Bant kısayolu: K1741 > K1780 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Tokenizer · Query Parser · Phrase · Filter
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 052 - MEDIA - TRANSCODE «DÖNÜŞÜM HATTI» K1781 > K1820

- Bant kısayolu: K1781 > K1820 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Codec · Ladder · ABR · Watermark
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 053 - MEDIA - DELIVERY «TESLİM HATTI» K1821 > K1860

- Bant kısayolu: K1821 > K1860 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Signed URL · Cookie · Token · QSF
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 054 - MEDIA - OFFLINE «ÇEVRİMDIŞI HATTI» K1861 > K1900

- Bant kısayolu: K1861 > K1900 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Download · DRM · Expiry · Repair
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 055 - AI - SAFETY DEEP «YAPAY ZEKÂ GUVENLIGI» K1901 > K1940

- Bant kısayolu: K1901 > K1940 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Prompt Injection · Output Filter · Red Team
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 056 - AI - EVAL DEEP «YAPAY ZEKÂ OLCUMU» K1941 > K1980

- Bant kısayolu: K1941 > K1980 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Golden Set · Judge · Drift · Regression
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 057 - HARDWARE - ACOUSTICS «AKUSTİK» K1981 > K2020

- Bant kısayolu: K1981 > K2020 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Enclosure · Port Tuning · Damping · Mic Cal
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 058 - HARDWARE - POWER DEEP «GÜÇ DERİNLİĞİ» K2021 > K2060

- Bant kısayolu: K2021 > K2060 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Topology · Bulk Cap · Soft Start · EMI Filter
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 059 - HARDWARE - THERMAL DEEP «ISI DERİNLİĞİ» K2061 > K2100

- Bant kısayolu: K2061 > K2100 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Fin Pitch · TIM · Sensor Map · Curve
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 060 - HARDWARE - PCB DEEP «PCB DERİNLİĞİ» K2101 > K2140

- Bant kısayolu: K2101 > K2140 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Stackup · Impedance · Return Path · Shielding
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 061 - MANUFACTURING - TEST «ÜRETİM TESTI» K2141 > K2180

- Bant kısayolu: K2141 > K2180 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: ICT · FCT · Audio Test · Burn-in
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 062 - MANUFACTURING - SUPPLY «TEDARIK» K2181 > K2220

- Bant kısayolu: K2181 > K2220 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Second Source · Lead Time · EOL · Audit
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 063 - COMMERCIAL - SUBSCRIPTION «ABONELİK DERİNLİĞİ» K2221 > K2260

- Bant kısayolu: K2221 > K2260 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Trial · Proration · Dunning · Winback
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 064 - COMMERCIAL - ADS «REKLAM» K2261 > K2300

- Bant kısayolu: K2261 > K2300 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Ad Slot · Frequency · Measurement · Consent
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 065 - COMMERCIAL - PARTNER «İŞ ORTAĞI» K2301 > K2340

- Bant kısayolu: K2301 > K2340 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: API Partner · Revenue Share · Certification
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 066 - PLATFORM - MULTI-TENANT «ÇOK KİRALI PLATFORM» K2341 > K2380

- Bant kısayolu: K2341 > K2380 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Isolation · Quota · Metering · Per-Tenant Policy
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 067 - PLATFORM - API LIFECYCLE «API YAŞAM DÖNGÜSÜ» K2381 > K2420

- Bant kısayolu: K2381 > K2420 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Deprecation · Sunset · Version Negotiation
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 068 - PLATFORM - SDK «SDK KATMANI» K2421 > K2460

- Bant kısayolu: K2421 > K2460 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Client Gen · Auth Helper · Retry · Telemetry
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 069 - PLATFORM - WEBHOOK «WEBHOOK KATMANI» K2461 > K2500

- Bant kısayolu: K2461 > K2500 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Signature · Retry Queue · Idempotency · DLQ
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 070 - NETWORK - TRANSPORT «TASIMA KATMANI» K2501 > K2540

- Bant kısayolu: K2501 > K2540 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: QUIC · TLS 1.3 · HTTP/3 · Congestion
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 071 - NETWORK - TOPOLOGY «TOPOLOJİ» K2541 > K2580

- Bant kısayolu: K2541 > K2580 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Anycast · Peering · Failover · Latency Map
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 072 - SECURITY - IDENTITY DEEP «KİMLİK DERİNLİĞİ» K2581 > K2620

- Bant kısayolu: K2581 > K2620 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: OAuth/OIDC · Passkey · MFA · Session Bind
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 073 - SECURITY - APP DEEP «UYGULAMA GÜVENLİĞİ» K2621 > K2660

- Bant kısayolu: K2621 > K2660 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: SAST · DAST · Fuzz · SBOM
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 074 - SECURITY - CRYPTO DEEP «ŞİFRELEME DERİNLİĞİ» K2661 > K2700

- Bant kısayolu: K2661 > K2700 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Key Rotation · HSM · Envelope · Nonce
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 075 - DATA - STREAMING DEEP «AKIŞ VERİSİ» K2701 > K2740

- Bant kısayolu: K2701 > K2740 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Exactly-once · Watermark · Window · Backlog
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 076 - DATA - WAREHOUSE «VERİ AMBARI» K2741 > K2780

- Bant kısayolu: K2741 > K2780 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Dimensional Model · SCD · Aggregate
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 077 - DATA - LAKE «VERİ GÖLÜ» K2781 > K2820

- Bant kısayolu: K2781 > K2820 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Bronze/Silver/Gold · Schema Evolution · Compaction
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 078 - UI - DESIGN SYSTEM «TASARIM SİSTEMİ» K2821 > K2860

- Bant kısayolu: K2821 > K2860 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Token · Primitive · Pattern · Doc
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 079 - UI - MOTION «HAREKET» K2861 > K2900

- Bant kısayolu: K2861 > K2900 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Transition · Easing · Reduced Motion · Perf
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 080 - UI - ACCESSIBILITY «ERİŞİLEBİLİRLİK» K2901 > K2940

- Bant kısayolu: K2901 > K2940 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: WCAG 2.1 AA · Focus Order · ARIA · Contrast
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 081 - UI - PERSONALIZATION UX «KİŞİSELLEŞTİRİLMİŞ ARAYÜZ» K2941 > K2980

- Bant kısayolu: K2941 > K2980 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Home Row · Shelf · Empty State · Nudge
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 082 - AGENT - ORCHESTRATION DEEP «AJAN ORKESTRASYONU» K2981 > K3020

- Bant kısayolu: K2981 > K3020 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Router · Budget · Handoff · Checkpoint
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 083 - AGENT - TOOLING «AJAN ARAÇLARI» K3021 > K3060

- Bant kısayolu: K3021 > K3060 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Schema · Sandbox · Rate Limit · Audit
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 084 - AGENT - MEMORY «AJAN HAFIZASI» K3061 > K3100

- Bant kısayolu: K3061 > K3100 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Episodic · Semantic · Compaction · Forget
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 085 - AGENT - MULTI «ÇOK AJAN» K3101 > K3140

- Bant kısayolu: K3101 > K3140 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Role Split · Consensus · Conflict · Merge
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 086 - SIM - ROOM ACOUSTICS «ODA AKUSTİĞİ SIM.» K3141 > K3180

- Bant kısayolu: K3141 > K3180 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: RT60 · Image Source · Absorption
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 087 - SIM - LOUDSPEAKER «HOPARLÖR SIM.» K3181 > K3220

- Bant kısayolu: K3181 > K3220 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Thiele-Small · Xmax · Crossover Sim
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 088 - SIM - POWER «GÜÇ SİM.» K3221 > K3260

- Bant kısayolu: K3221 > K3260 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Load Profile · Transient · Efficiency
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 089 - SIM - NETWORK «AĞ SİM.» K3261 > K3300

- Bant kısayolu: K3261 > K3300 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Jitter · Loss · Buffer Sim · Congestion
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 090 - SIM - CODEC «KODEK SIM.» K3301 > K3340

- Bant kısayolu: K3301 > K3340 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: ABR Ladder · Psychoacoustic · Artifact
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 091 - RESEARCH - PERCEPTION «ALGI ARAŞTIRMASI» K3341 > K3380

- Bant kısayolu: K3341 > K3380 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Loudness · Masking · MOS Test
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 092 - RESEARCH - ML «MAKİNE ÖĞRENME ARASTIRMASI» K3381 > K3420

- Bant kısayolu: K3381 > K3420 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Baseline · Ablation · Scaling
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 093 - RESEARCH - ACOUSTICS «AKUSTİK ARAŞTIRMA» K3421 > K3460

- Bant kısayolu: K3421 > K3460 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Measurement Mic · Sweeps · Anechoic
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 094 - GOVERNANCE - ADR DEEP «ADR DERİNLİĞİ» K3461 > K3500

- Bant kısayolu: K3461 > K3500 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Context · Alternatives · Consequence
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 095 - GOVERNANCE - STANDARD «STANDART» K3501 > K3540

- Bant kısayolu: K3501 > K3540 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: RFC Adopt · Normative Ref · Errata
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 096 - GOVERNANCE - TRAINING «EĞİTİM» K3541 > K3580

- Bant kısayolu: K3541 > K3580 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Onboarding · Playbook · Review Drill
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 097 - COMPLIANCE - PRIVACY «GİZLİLİK» K3581 > K3620

- Bant kısayolu: K3581 > K3620 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: DPIA · Consent · Erasure · Portability
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 098 - COMPLIANCE - LICENSE «LİSANS» K3621 > K3660

- Bant kısayolu: K3621 > K3660 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: OSS Scan · Copyleft · Notice File
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 099 - OBSERVABILITY - SLO «SLO KATMANI» K3661 > K3700

- Bant kısayolu: K3661 > K3700 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: SLI · Error Budget · Burn Rate · Alert
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 100 - OBSERVABILITY - COST «MALİYET GOZLEMI» K3701 > K3740

- Bant kısayolu: K3701 > K3740 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Cardinality · Sample · Retention
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 101 - DELIVERY - RELEASE «SÜRÜM KATMANI» K3741 > K3780

- Bant kısayolu: K3741 > K3780 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: SemVer · Changelog · Feature Flag · Canary
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 102 - DELIVERY - IAC «ALTYAPI KODU» K3781 > K3820

- Bant kısayolu: K3781 > K3820 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Terraform · Drift · Policy as Code
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 103 - DELIVERY - CONTAINER «KONTEYNER» K3821 > K3860

- Bant kısayolu: K3821 > K3860 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Image Sig · SBOM · Scan · Slim
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 104 - DEVEX - TOOLCHAIN «ARAÇ ZİNCİRİ» K3861 > K3900

- Bant kısayolu: K3861 > K3900 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Lint · Format · Type Check · Pre-commit
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 105 - DEVEX - DEBUGGING «HATA AYIKLAMA» K3901 > K3940

- Bant kısayolu: K3901 > K3940 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Repro · Bisect · Trace · Memory Dump
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 106 - DEVEX - TEST «TEST PLATFORMU» K3941 > K3980

- Bant kısayolu: K3941 > K3980 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Fixture · Snapshot · Fuzz · Perf Test
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 107 - COMMERCE - TAX «VERGİ» K3981 > K4020

- Bant kısayolu: K3981 > K4020 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: VAT · MOSS · Nexus · Invoice Rule
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 108 - COMMERCE - FRAUD «DOLANDIRICILIK» K4021 > K4060

- Bant kısayolu: K4021 > K4060 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Velocity · Device Fingerprint · Review
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 109 - RIGHTS - CATALOG «KATALOG HAKKI» K4061 > K4100

- Bant kısayolu: K4061 > K4100 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: ISRC · ISWC · DPLP · Takedown
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 110 - RIGHTS - ROYALTY «ROYALTY» K4101 > K4140

- Bant kısayolu: K4101 > K4140 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Split · Statement · Advance · Audit Right
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 111 - MEDIA - METADATA «METAVERİ» K4141 > K4180

- Bant kısayolu: K4141 > K4180 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: ID3 · CD Tags · Schema · Enrichment
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 112 - MEDIA - ARTWORK «ALBÜM KAPAĞI» K4181 > K4220

- Bant kısayolu: K4181 > K4220 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Thumbnail · Palette · Lazy Load
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 113 - MEDIA - LYRICS «ŞARKI SÖZÜ» K4221 > K4260

- Bant kısayolu: K4221 > K4260 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Timing · Sync · License · Fallback
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 114 - NETWORK - MULTI-ROOM NET «ÇOKLU ODA AĞI» K4261 > K4300

- Bant kısayolu: K4261 > K4300 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Clock · Group · Buffer Align · Handoff
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 115 - NETWORK - CAR NET «ARAÇ AĞI» K4301 > K4340

- Bant kısayolu: K4301 > K4340 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Tether · Offline Cache · Safety Lock
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 116 - NETWORK - STUDIO NET «STÜDYO AĞI» K4341 > K4380

- Bant kısayolu: K4341 > K4380 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Dante-like · Clock · Redundancy
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 117 - SECURITY - FIRMWARE «FIRMWARE GÜVENLİĞİ» K4381 > K4420

- Bant kısayolu: K4381 > K4420 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Secure Boot · Signed Update · Rollback Protect
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 118 - SECURITY - DRIVER «SÜRÜCÜ GÜVENLİĞİ» K4421 > K4460

- Bant kısayolu: K4421 > K4460 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Ring0 Cap · IOCTL Audit · Fuzz
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 119 - SECURITY - CONTENT «İÇERİK GÜVENLİĞİ» K4461 > K4500

- Bant kısayolu: K4461 > K4500 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: DRM · Watermark · Session Bind
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 120 - AI - PERSONALIZATION DEEP «KİŞİSELLEŞTİRME DERİNLİĞİ» K4501 > K4540

- Bant kısayolu: K4501 > K4540 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Taste Profile · Context Signal · Cold Start
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 121 - AI - VOICE «SESLİ ARAYÜZ» K4541 > K4580

- Bant kısayolu: K4541 > K4580 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: ASR · Intent · TTS · Barge-in
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 122 - AI - AUTO EQ «OTOMATİK EQ» K4581 > K4620

- Bant kısayolu: K4581 > K4620 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Target Curve · Room Match · Confidence
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 123 - AI - EDGE «KENAR YAPAY ZEKÂSI» K4621 > K4660

- Bant kısayolu: K4621 > K4660 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Quantization · NPU · Power Budget
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 124 - DATA - LOCAL FIRST «YEREL ÖNCELİKLİ» K4661 > K4700

- Bant kısayolu: K4661 > K4700 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: SQLite · CRDT · Sync Log · Conflict
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 125 - DATA - BACKUP DEEP «YEDEKLEME DERİNLİĞİ» K4701 > K4740

- Bant kısayolu: K4701 > K4740 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Restic Repo · Immutable · Test Restore
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 126 - UX - ONBOARDING «TANITIM AKIŞI» K4741 > K4780

- Bant kısayolu: K4741 > K4780 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: First Play · Permission · Education
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 127 - UX - NOTIFICATION UX «BİLDİRİM ARAYÜZÜ» K4781 > K4820

- Bant kısayolu: K4781 > K4820 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Channel · Priority · Quiet Hours
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 128 - UX - ERROR UX «HATA ARAYÜZÜ» K4821 > K4860

- Bant kısayolu: K4821 > K4860 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Retry · Fallback · Offline State
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 129 - COMMERCE - STORE «MAĞAZA» K4861 > K4900

- Bant kısayolu: K4861 > K4900 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Entitlement · Receipt · Restore
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 130 - PLATFORM - DEVEX «GELİŞTİRİCİ DENEYİMİ» K4901 > K4940

- Bant kısayolu: K4901 > K4940 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Quickstart · Playground · Console
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 131 - PLATFORM - EXTENSIBILITY «GENİŞLETİLEBİLİRLİK» K4941 > K4980

- Bant kısayolu: K4941 > K4980 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Hook · Event Bus · Custom Field
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 132 - ENTERPRISE - SCALE OUT «ÖLÇEKLENME» K4981 > K5020

- Bant kısayolu: K4981 > K5020 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Cell · Zone · Regional Active-Active
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 133 - ENTERPRISE - RESILIENCE «DAYANIKLILIK» K5021 > K5060

- Bant kısayolu: K5021 > K5060 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Bulkhead · Circuit Breaker · Retry Budget
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 134 - ENTERPRISE - CHAOS «KAOS MÜHENDİSLİĞİ» K5061 > K5100

- Bant kısayolu: K5061 > K5100 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Game Day · Fault Injection · Blast Radius
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 135 - ENTERPRISE - CAPACITY «KAPASİTE» K5101 > K5140

- Bant kısayolu: K5101 > K5140 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Forecast · Headroom · Pre-scale
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 136 - FUTURE - SPATIAL «UZAMSAL BİLGİSAYAR» K5141 > K5180

- Bant kısayolu: K5141 > K5180 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Scene · Anchor · Occlusion
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 137 - FUTURE - AVATARS «AVATAR» K5181 > K5220

- Bant kısayolu: K5181 > K5220 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Rig · Lip Sync · Presence
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 138 - FUTURE - CLOUD GAMING «BULUT OYUN» K5221 > K5260

- Bant kısayolu: K5221 > K5260 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Input Latency · Frame Stream
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 139 - FUTURE - HEALTH AUDIO «SAĞLIK SESİ» K5261 > K5300

- Bant kısayolu: K5261 > K5300 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Hearing Test · Safe Level · Care
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 140 - FUTURE - EDUCATION «EĞİTİM» K5301 > K5340

- Bant kısayolu: K5301 > K5340 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Course · Exercise · Progress
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 141 - FUTURE - TELECOM «TELEKOM» K5341 > K5380

- Bant kısayolu: K5341 > K5380 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Carrier Billing · Roaming · QoS
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 142 - FUTURE - PAYMENTS WALLET «CUZDAN» K5381 > K5420

- Bant kısayolu: K5381 > K5420 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Ledger · Reconcile · Payout
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 143 - FUTURE - SMART CITY SES «ŞEHİR SES AĞI» K5421 > K5460

- Bant kısayolu: K5421 > K5460 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Noise Map · Zoning · Opt-out
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 144 - FUTURE - ROBOTICS AUDIO «ROBOTİK SES» K5461 > K5500

- Bant kısayolu: K5461 > K5500 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: Beamforming · VAD · Echo Cancel
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 145 - EXPANSION - RESERVED 1 «REZERVE 1» K5501 > K5540

- Bant kısayolu: K5501 > K5540 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5501-K5540) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 146 - EXPANSION - RESERVED 2 «REZERVE 2» K5541 > K5580

- Bant kısayolu: K5541 > K5580 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5541-K5580) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 147 - EXPANSION - RESERVED 3 «REZERVE 3» K5581 > K5620

- Bant kısayolu: K5581 > K5620 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5581-K5620) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 148 - EXPANSION - RESERVED 4 «REZERVE 4» K5621 > K5660

- Bant kısayolu: K5621 > K5660 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5621-K5660) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 149 - EXPANSION - RESERVED 5 «REZERVE 5» K5661 > K5700

- Bant kısayolu: K5661 > K5700 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5661-K5700) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 150 - EXPANSION - RESERVED 6 «REZERVE 6» K5701 > K5740

- Bant kısayolu: K5701 > K5740 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5701-K5740) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 151 - EXPANSION - RESERVED 7 «REZERVE 7» K5741 > K5780

- Bant kısayolu: K5741 > K5780 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5741-K5780) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 152 - EXPANSION - RESERVED 8 «REZERVE 8» K5781 > K5820

- Bant kısayolu: K5781 > K5820 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5781-K5820) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 153 - EXPANSION - RESERVED 9 «REZERVE 9» K5821 > K5860

- Bant kısayolu: K5821 > K5860 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5821-K5860) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 154 - EXPANSION - RESERVED 10 «REZERVE 10» K5861 > K5900

- Bant kısayolu: K5861 > K5900 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5861-K5900) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 155 - EXPANSION - RESERVED 11 «REZERVE 11» K5901 > K5940

- Bant kısayolu: K5901 > K5940 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5901-K5940) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 156 - EXPANSION - RESERVED 12 «REZERVE 12» K5941 > K5980

- Bant kısayolu: K5941 > K5980 (40 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5941-K5980) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

### BANT 157 - EXPANSION - RESERVED 13 «REZERVE 13» K5981 > K5999

- Bant kısayolu: K5981 > K5999 (19 katman) - tam kart gerektiğinde EK C ile üretilir; bu bant seviyesinde Sorumluluk: TBD — genişleme için ayrıldı (K5981-K5999) · Genişleme kapısı: ADR + onay ile doldurulur · Yeni domain girişi: entegrasyon dinamiği (section 4.7)
- Kanıt: kaynak prompt genişleme taksonomisi + ⚠️ VERIFICATION REQUIRED (web)

## A.4 — ARALIK ÖZET TABLOSU (decomposition haritası)

| Aralik | Rol | Derinlik Formulu |
|---|---|---|
| K000-K020 | EXISTING FOUNDATION | 21 çekirdek katman - tek seviye isimlendirme |
| K021-K120 | ENTERPRISE EXPANSION | 100 domain katmani - alt madde (Kx.y) 5-8 |
| K121-K500 | SPECIALIZED DOMAINS | 19 bant x ~20 katman - bant bazli |
| K501-K1020 | EXTENDED / DEEP PLATFORM | omurga · olcek · veri · ops · guven |
| K1021-K2020 | DEEP DOMAIN / RUNTIME / CROSS-CUT | otomotiv · ev · studyo · runtime · güvenlik |
| K2021-K3020 | HARDWARE / MEDIA / UI DEEP | donanim · üretim · ticaret · platform · ag · ui |
| K3021-K4020 | AGENT / SIM / RESEARCH / GOV | ajan · simulasyon · arastirma · yonetisim · teslimat |
| K4021-K5020 | COMMERCE / RIGHTS / AI / DATA | haklar · medya · güvenlik · ai · veri · ux |
| K5021-K5999 | RESILIENCE / FUTURE / RESERVED | dayaniklilik · gelecek alanlari · rezerv |

```
DECOMPOSITION ZINCIRI (her katmanda zorunlu):

 K-Layer
   +- Domain
        +- Subdomain
             +- Bounded Context
                  +- Aggregate
                       +- Module
                            +- Component
                                 +- Service
                                      +- Adapter
                                           +- Implementation
```

## A.5 — SAYAÇ ÖZETİ

- K000-K020   : 21 katman (foundation) - tam kimlik kartı
- K021-K120   : 100 katman (enterprise + professional) - tam kimlik kartı
- K121-K999   : 879 katman - tam kimlik kartı (bant bazli)
- K1000-K5999 : 5000 katman - bant seviyesinde özet (157 bant)
- TOPLAM      : 6000 katman tanımı kapsamlı



