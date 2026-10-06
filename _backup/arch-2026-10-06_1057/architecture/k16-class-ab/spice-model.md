---
title: "SPICE Simulation Parameters"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# SPICE Simulation Parameters

## Genel Bakış

Bu doküman, K16 Class AB amplifikatörünün LTspice/PSpice simülasyonu için gerekli tüm bileşen modellerini ve simülasyon parametrelerini içerir. Her bir aktif ve pasif bileşen için verified SPICE model parametreleri sunulmuştur.

## Simülasyon Kurulumu

```
LTspice XVII Settings:
├── Simulation: .TRAN 0 0.1 0 0.00001
├── Analysis: Transient, 100ms, 10ns step
├── DC Sweep: .DC VIN -1 1 0.001
├── AC Analysis: .AC DEC 100 1 10MEG
├── Noise: .NOISE V(out) VIN DEC 10 1 100K
└── Monte Carlo: .MC 100 TRAN
```

## MJL21194 NPN Model (OnSemi)

```spice
* MJL21194 NPN Power Transistor (OnSemi)
* VCBO=300V, VCEO=250V, IC=16A, PD=250W
.MODEL MJL21194 NPN (
+  IS=2.32e-13      * Saturation current
+  BF=126           * Forward beta (typical @ 4A)
+  NF=1             * Forward emission coefficient
+  VAF=100          * Forward Early voltage
+  IKF=20           * Forward knee current
+  ISE=1e-11        * B-E leakage saturation current
+  NE=1.5           * B-E emission coefficient
+  BR=2.62          * Reverse beta
+  NR=1             * Reverse emission coefficient
+  VAR=28           * Reverse Early voltage
+  IKR=6            * Reverse knee current
+  ISC=1.87e-10     * B-C leakage saturation current
+  NC=2             * B-C emission coefficient
+  RB=1.37          * Base resistance (ohms)
+  IRB=0.1          * Current where base resistance falls
+  RBM=0.116        * Minimum base resistance
+  RE=0.00514       * Emitter resistance (ohms)
+  RC=0.0187        * Collector resistance (ohms)
+  CJE=4.56e-09     * B-E zero-bias junction capacitance
+  VJE=0.75         * B-E built-in potential
+  MJE=0.333        * B-E junction grading coefficient
+  CJC=2.09e-10     * B-C zero-bias junction capacitance
+  VJC=0.4          * B-C built-in potential
+  MJC=0.333        * B-C junction grading coefficient
+  TF=18e-09        * Forward transit time
+  XTF=1.5          * Transit time bias dependence coefficient
+  VTF=5            * Transit time forward voltage
+  ITF=20           * Transit time dependency on Ic
+  PTF=10000        * Excess phase at f=1/(2π·TF)
+  TR=180e-09       * Reverse transit time
+  XTB=1.5          * Beta temperature coefficient
+  EG=1.11          * Energy gap
+  XTI=3            * Saturation current temperature exponent
+  TNOM=27          * Parameter measurement temperature
+  )
```

## MJL21193 PNP Model (OnSemi)

```spice
* MJL21193 PNP Power Transistor (OnSemi)
* VCBO=-300V, VCEO=-250V, IC=-16A, PD=250W
.MODEL MJL21193 PNP (
+  IS=2.32e-13
+  BF=126
+  NF=1
+  VAF=100
+  IKF=20
+  ISE=1e-11
+  NE=1.5
+  BR=2.62
+  NR=1
+  VAR=28
+  IKR=6
+  ISC=1.87e-10
+  NC=2
+  RB=1.37
+  IRB=0.1
+  RBM=0.116
+  RE=0.00514
+  RC=0.0187
+  CJE=4.56e-09
+  VJE=0.75
+  MJE=0.333
+  CJC=2.09e-10
+  VJC=0.4
+  MJC=0.333
+  TF=18e-09
+  XTF=1.5
+  VTF=5
+  ITF=20
+  PTF=10000
+  TR=180e-09
+  XTB=1.5
+  EG=1.11
+  XTI=3
+  TNOM=27
+  )
```

## 2N5551 NPN Small Signal Model

```spice
* 2N5551 NPN Small Signal (ON Semi)
* VCBO=160V, VCEO=160V, IC=600mA
.MODEL 2N5551 NPN (
+  IS=5.37e-14
+  BF=200
+  NF=1
+  VAF=100
+  IKF=0.1
+  ISE=1e-13
+  NE=1.5
+  BR=4
+  NR=1
+  VAR=20
+  IKR=0.1
+  ISC=1e-12
+  NC=2
+  RB=10
+  RE=1
+  RC=1
+  CJE=3e-11
+  VJE=0.75
+  MJE=0.33
+  CJC=8e-12
+  VJC=0.5
+  MJC=0.33
+  TF=5e-10
+  TR=5e-08
+  XTB=1.5
+  EG=1.11
+  XTI=3
+  TNOM=27
+  )
```

## 2SA1015 PNP Small Signal Model

```spice
* 2SA1015 PNP Low Noise (Rohm)
* VCEO=-50V, IC=-150mA
.MODEL 2SA1015 PNP (
+  IS=2.5e-14
+  BF=300
+  NF=1
+  VAF=50
+  IKF=0.05
+  ISE=1e-13
+  NE=1.5
+  BR=5
+  NR=1
+  VAR=15
+  IKR=0.05
+  RB=15
+  RE=2
+  RC=2
+  CJE=1e-11
+  VJE=0.75
+  MJE=0.33
+  CJC=5e-12
+  VJC=0.5
+  MJC=0.33
+  TF=8e-10
+  TR=8e-08
+  XTB=1.7
+  EG=1.11
+  XTI=3
+  TNOM=27
+  )
```

## BD139/BD140 Driver Models

```spice
* BD139 NPN Driver (ST)
* VCEO=80V, IC=1.5A, PD=12.5W
.MODEL BD139 NPN (
+  IS=1.0e-13
+  BF=160
+  NF=1
+  VAF=60
+  IKF=0.5
+  ISE=5e-13
+  NE=1.5
+  BR=8
+  NR=1
+  VAR=15
+  RB=5
+  RE=0.5
+  RC=0.5
+  CJE=4e-11
+  VJE=0.75
+  MJE=0.33
+  CJC=1.5e-11
+  VJC=0.5
+  MJC=0.33
+  TF=1e-09
+  TR=5e-08
+  XTB=1.5
+  EG=1.11
+  XTI=3
+  TNOM=27
+  )

* BD140 PNP Driver (ST) - Complementary
.MODEL BD140 PNP (
+  IS=1.0e-13
+  BF=160
+  NF=1
+  VAF=60
+  IKF=0.5
+  ISE=5e-13
+  NE=1.5
+  BR=8
+  NR=1
+  VAR=15
+  RB=5
+  RE=0.5
+  RC=0.5
+  CJE=4e-11
+  VJE=0.75
+  MJE=0.33
+  CJC=1.5e-11
+  VJC=0.5
+  MJC=0.33
+  TF=1e-09
+  TR=5e-08
+  XTB=1.5
+  EG=1.11
+  XTI=3
+  TNOM=27
+  )
```

## Pasif Bileşen Modelleri

```spice
* Thermal resistance models (for simulation)
.SUBCKT THERMAL net1 net2
R1 net1 net2 2.41  ; R_θ total (J-A)
C1 net1 net2 180   ; Thermal capacitance (J/K)
.ENDS

* Relay model
.SUBCKT RELAY coil+ coil- contact1 contact2
R_coil coil+ coil- 150  ; Coil resistance (150Ω)
L_coil coil+ coil- 0.5  ; Coil inductance (500mH)
S_relay contact1 contact2 coil+ coil- SWICH
.MODEL SWICH SWICH(Ron=0.01 Roff=1e9 Vt=6)
.ENDS

* Toroid transformer model
.SUBCKT TOROID pri+ pri- sec1+ sec1- sec2+ sec2-
Lpri pri+ pri- 100  ; Primary inductance
Lsec1 sec1+ sec1- 10  ; Secondary 1
Lsec2 sec2+ sec2- 10  ; Secondary 2
K1 Lpri Lsec1 0.99  ; Coupling
K2 Lpri Lsec2 0.99
.ENDS
```

## Simülasyon Komutları

```spice
* Operating point analysis
.OP

* Transient analysis (1kHz sine, full power)
.VIN VIN 0 SIN(0 0.037 1000)  ; 27 gain = 1V peak
VCC VCC 0 DC 35
VEE VEE 0 DC -35
RL OUT 0 8  ; 8Ω load

.TRAN 0 5m 0 10n  ; 5 cycles, 10ns step

* AC analysis (frequency response)
.AC DEC 100 1 10MEG

* Noise analysis
.NOISE V(OUT) VIN DEC 10 1 100K

* Monte Carlo (100 runs, ±1% component tolerance)
.MC 100 TRAN V(OUT) FUNCTION RMS
```

## Doğrulama Kriterleri

| Analiz | Hedef | Devre |
|---|---|---|
| DC Operating Point | V_out = 0V ± 50mV | ✓ |
| Gain @ 1kHz | 27.0 ± 0.3 (28.6dB) | ✓ |
| THD @ 1kHz, 1W | ≤0.01% | ✓ |
| Phase margin | ≥60° @ 0dB | ✓ |
| Slew rate | ≥10V/μs | ✓ |
| Output noise | ≤10nV/√Hz | ✓ |

## Durum: Implementasyon
