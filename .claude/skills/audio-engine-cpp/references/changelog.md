# Changelog — audio-engine-cpp

Format: [Keep a Changelog](https://keepachangelog.com/) mantığı; sürüm alanı
`metadata.version` (frontmatter) ile senkron.

## [4.0.0] — 2026-10-21

**Sıfırdan yeniden yazım (onaylı reset).** 3.0.0 (98 satır iskelet + 5 ek dosya)
tamamen silindi; yerine disk-kanıtlı, Truth Mode kaynaklı içerik yazıldı.

### Added
- `references/contracts.md` (yeni) — katman/RT callback/sahiplik tabloları,
  `BackendResult`, `EqChain` 62 biquad sözleşmesi (disk kanıtı)
- `references/sources.md` (yeni) — Truth Mode kaynakları S1-S6 + disk kanıtları
  + §V bekleyen doğrulamalar
- `references/changelog.md` (yeni) — bu dosya
- `examples/add-band-eq.md` (yeni) — "EQ ekle" tam döngü örneği (gerçek EqChain
  kontratı ile istek→sözleşme→DSP→RT→ctest)

### Changed
- `SKILL.md`: 98 → 500+ satır (§1-§12); §10.1 "disk-öncelikli çelişkiler" bölümü
  eklendi (test framework, reverb modu, ASIO/JUCE entegrasyonu, kanal çelişkisi)
- `references/realtime-rules.md`: R1-R7 sistematizasyonu; doğru/yanlış kod
  çiftleri, hata masası, 10 maddelik RT denetim listesi
- `references/dsp-chain-map.md`: hedef ↔ disk fark tablosu; pipeline sırası
  (EQ→Reverb→Crossover→Limiter); kalite hedefleri (SNR>100dB, THD+N<0.01%)
- `references/latency-targets.md`: gecikme matematiği (512@48k = 10.67 ms);
  ASIO/WASAPI karşılaştırması; MMCSS "Pro Audio"; xrun teşhis tablosu
- `examples/add-reverb-mode.md`: yeniden yazım — reverb eklemek DUR + ADR/onay
  gerektirir (enum yok, §10.1.2)

### Removed
- 3.0.0 iskeletinin tamamı (5 dosya) — yeniden yazım kapsamında silinip yeniden üretildi:
  `SKILL.md` · `references/realtime-rules.md` · `references/dsp-chain-map.md` ·
  `references/latency-targets.md` · `examples/add-reverb-mode.md`

### Fixed (içerik doğruluğu)
- "Google Test" iddiası düzeltildi → disk gerçeği **ctest + `<cassert>`** (§10.1.1)
- JUCE 9 / ASIO SDK 2.3.4 sürüm kanıtları eklendi (S2/S3b/S3c; GA detayı §V.1)

## [3.0.0] — (önceki — silindi)

- İlk iskelet: 98 satır SKILL.md + 5 ek dosya (realtime-rules, latency-targets,
  dsp-chain-map, examples/add-reverb-mode, references/index).
- 4.0.0 reset gerekçesi: minimum satır sınırı altında; disk kanıtlarıyla
  hizasızdı (test framework, reverb modu, ASIO/JUCE entegrasyonu).
