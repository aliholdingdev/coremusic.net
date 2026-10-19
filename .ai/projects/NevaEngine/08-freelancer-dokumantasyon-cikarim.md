---
title: "Neva Engine — CoreMusic Freelancer Dokümanı v1.0 Çıkarımı"
type: doc
category: audio-engine
version: 1.0.0
status: aktif
authority: "Kaynak: kullanıcı yapıştırması 2026-10-15 (Freelancer Technical Documentation v1.0) · vault SSOT: 00/02/06"
updated: 2026-10-15
---

# Freelancer Dokümanı v1.0 — Vault'a Alınan Benzersiz Kalemler

**Zorunlu Bağlantılar:** [[06-gereksinimler-ve-farkliliklar]] · [[02-dsp-zincir-mimari]] · [[brain.md]] §7.4 · [[../04-reverse-voicemeeter-fxsound]] (bakınız, bu dosya yeni RE turudur)

## §0 Kaynak künyesi

Kullanıcı 2026-10-15'te "CoreMusic Freelancer Technical Documentation Version 1.0" başlıklı dokümanın tam metnini sohbete yapıştırdı (Giriş · Sistem Mimarisi bölümleri). Bu dosya, dokümandan **vault'a henüz işlenmemiş benzersiz kalemleri** çıkarır; doküman geneli zaten `VISION.md` / `PROJECTS.md` / `.ai/architecture/**` ile örtüşür. Çelişkide vault SSOT kazanır.

## §1 Neva Engine'e ait spesifik ifadeler (dokümandan, birebir özet)

| İfade | Vault karşılığı / durum |
|-------|-------------------------|
| "C++20 Neva Engine … OS mikserini baypas eden çekirdek" | 00/02 ile uyumlu |
| "32-bit float DSP · **True Peak brickwall limiter** · **psikoakustik reverb**" | R10 (limiter) ile uyumlu; **psikoakustik reverb → R6 mekan preset'leri** |
| "**Düğün Salonu, Konser Alanı & Arena, Canlı Stüdyo, Kulüp** mekan modelleri" | **YENİ** → R6'ya işlendi (4 hazır psikoakustik mekan) |
| "31-band **parametrik EQ ve grafik EQ iki mod**; web panelinden ayar, özel media sunucusu üzerinden **Web Audio API'ye stream**" | R16 (EQ çift form) ile birebir uyumlu · web panel = yerel PHP (Q2) · stream = 05 §5 Web Audio editör |
| "True Peak limiter **THD+N <%0.01 · SNR >100dB**" | Vault spec **daha sıkı**: THD+N <0.005% · SNR >105dB (00-sdk) — vault kazanır |
| "32-bit float **1528 dB** teorik dinamik tavan" | Dikkat: 32-bit float SNR ≈ 1528 dB (kaynak: IEEE754 mantığı) — tutarlı, kabul |
| "**64-bit float yol haritası** (double precision AR-GE)" | **YENİ** → Faz 5+ notu (01 §fazlara eklenecek) |
| "1.0→8.1 surround (+1 LFE) akıllı yönlendirme matrisi · araç 4.1/5.1" | ADR-038 + 05 §3.1 matrix ile uyumlu |
| "EBU R128 / ITU-R BS.1770 **LUFS ölçer** · **2048-nokta FFT** spektrum · faz korelasyon" | 02 §ölçüm; 2048 FFT = 05 §2 meter ile uyumlu (detay işlenecek) |
| "ASIO **<10ms** · bit-perfect · **XMOS XU316** USB Audio 2.0 · Class AB 100W · **SNR 112dB**" | Vault ADR-019/038/089 ile uyumlu |
| "Native C++ player Win/Linux/macOS · **hem GUI hem CLI**" | **GUI+CLI** kalemi 01'e not edilecek (CLI = headless servis, overview.md §5 ile örtüşür) |
| "CarPlay / Android Auto · car.coremusic.net (RPi5+PCM3168A) · home.coremusic.net (RPi5)" | K000/K003 domainleri; Neva dışı (CoreMusic ekosistem) — bu dosyada yalnız işaret |
| "Multi-room: WebRTC + WebSocket, sıfıra yakın faz gecikmesi" | 04 RE (VBAN eşdeğeri) + K014 network |

## §2 İşlenen yeni gereksinimler

1. **R6 genişletildi:** psikoakustik mekan preset'leri (Düğün Salonu · Konser Alanı & Arena · Canlı Stüdyo · Kulüp) — 06'ya işlendi (2026-10-15).
2. **GUI+CLI notu:** native player her iki modda (01 §0'a eklenecek — FREKANS: düşük, Faz 3).
3. **64-bit float yol haritası:** Faz 5+ AR-GE notu olarak işaretlendi (01).
4. **LUFS + 2048 FFT:** 02 §ölçüm hedeflerine referans (EBU R128 / BS.1770).

## §3 Öncelik/ürün NOTU (doküman dili)

Doküman "Neva Engine = stüdyo referansı + medya omurgası" der; session kararları (10 çıkış A1-A10, dinamik≠bas, EQ çift form, mekan reverb, mobil DSP) bunun **DSP/ürün katmanıdır** — çelişki yok, tamamlayıcıdır.
