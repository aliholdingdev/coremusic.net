---
title: "Doğrulama & Referanslar"
type: guide
category: ecosystem
version: 1.0.1
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-09-29
---# Doğrulama & Referanslar## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] durum: active, tarih: 2026-09-24, kaynak alanları mevcut
- [ ] Tek H1 var, # ile başlıyor
- [ ] §4.1 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §1-§7 başlıkları eksiksiz (§3.7 Entegrasyon dahil)
- [ ] Kapsam / Kapsam Dışı tablosu ≥ 3 satır
- [ ] Zorunlu Bağlantılar satırı en az 1 wiki-link taşıyor
- [ ] §3.4 çapraz referans 21 K satırı içeriyor (K0-K20)
- [ ] §3.5 ders → K matrisi her satırda K katmanı ve SOMUT giriş barındırıyor
- [ ] §4.4 lisans kapısı 5 satır (MIT/GPL/AGPL/LGPL/ASIO SDK)
- [ ] §5.1 adım listesi ≥ 4 adım
- [ ] §6.1'de - [ ] maddeleri ≥ 8
- [ ] §7.2 Değişiklik Geçmişi append-only tablo var
- [ ] Authority footer (Authority + Last Updated + Mode) var
- [ ] Mojibake yok: node .ai/scripts/vault-utf8-writer.mjs verify → mojibake 0, BOM false
- [ ] 6 ana dosyanın her biri ≥ 500 satır (ölçüm: dosya listesi × satır sayısı raporu)
- [ ] 0 dosya silindi (git status delete = 0)
- [ ] Yeni web araştırması yapılmadı; tüm veri exa 2026-09-24 doğrulaması

### §6.2 Metrikler

| Metrik | Değer |
|--------|-------|
| Ekosistem dizini toplam dosya | 8 (2 mevcut + 6 yeni) |
| Yeni ana doküman | 6 (her biri ≥ 500 satır) |
| Kapsanan K katmanı | 19/21 (K7, K13 hariç — atama bekliyor) |
| Kapsanan lisans sınıfı | 5 (MIT ailesi, GPL, AGPL, LGPL, ASIO SDK) |
| Doğrulama kaynağı | exa web doğrulaması — 2026-09-24 |
| Versiyon | 1.0.0 |
| Silinen dosya | 0 |

### §6.3 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| K eşleşmesi eksik | §3.4/§3.5 satırı boş | github-referanslari §6/§7 ile senkronla |
| Lisans etiketi yok | Dosya §4.4'süz | Lisans tablosunu ekle, yoksa VERIFICATION REQUIRED |
| Satır < 500 | verify lines düşük | §3.5/§5 derinleştir; silme yapma |
| Çift SSOT iddiası | "ekosistem tek kaynaktır" | authority: reference'a çek |
| Mojibake | verify mojibake > 0 | vault-utf8-writer repair |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| [[../CLAUDE.md]] | Vault anayasası — Guardrail #1/#3/#16 kaynağı |
| [[../AGENTS.md]] | Agent routing §6 + DevOps okuma listesi §24.3 |
| [[../architecture/index]] | K0-K20 katman SSOT'u |
| [[../architecture/github-referanslari]] | GitHub referans havuzu (bu dizinin veri kaynağı) |
| [[../brain]] | ADR defteri — JUCE/ASIO kararları buraya açılır |
| [[../.templates/documentation/docs-md-template]] | Bu dosyanın şablonu (Guardrail #16) |
| [[../.templates/index]] | Şablon registry'si |
| [[README]] | Ekosistem servis/panel özeti (mevcut) |
| [[service-integration]] | Entegrasyon protokolleri + event types (mevcut) |
| [[muzik-streaming-sunuculari]] | Kardeş dosya — Koel/Ampache/Mopidy |
| [[ses-dsp-acik-kaynak]] | Kardeş dosya — JUCE/Dusk/OpenStudio |
| [[donanım-devre-referanslari]] | Kardeş dosya — donanım |
| [[ekosistem-mimarileri]] | Kardeş dosya — Spotify/Apple/YT Music |
| [[asio-wasapi-rehber]] | Kardeş dosya — sürücü/lisans |
| [[../log.md]] | Audit trail (append-only) |

### §7.2 Kaynak Linkleri (Web — exa doğrulaması 2026-09-24)

- https://www.steinberg.net/developers/asiosdk-open/ — ASIO SDK açık kaynak lisans sayfası
- https://github.com/juce-framework/JUCE — JUCE framework
- https://github.com/koel/koel — Koel müzik sunucusu
- https://github.com/ampache/ampache — Ampache müzik sunucusu
- https://github.com/dusk-audio/dusk-studio — Dusk Studio (IPC dersi)
- https://github.com/ddabidov/Headphone-DAC-AMP — XU316 + ES9039 referansı
- https://github.com/EliMattingly22/TPA3255_ClassD_PBTL — TPA3255 Class D PBTL
- https://github.com/hamzadenizyilmaz/BoostCore-Module — MT3608 boost modülü

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-20 | 1.0.0 | Ekosistem dizini ilk kez açıldı (README.md, service-integration.md) | vault-updater |
| 2026-09-24 | 1.0.0 | index.md üretildi — 6 ekosistem dosyası, K çapraz referans, lisans kapısı (exa doğrulaması) | ekosistem-writer (subagent) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
