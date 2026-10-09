---
title: "Workflow"
type: guide
category: ecosystem
version: 1.0.1
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-09-29
---# Workflow## §5 Workflow

### §5.1 Ekosistem Dersi İşleme Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | İlgili ekosistem dosyasını oku (§2.2'ye göre) | Ders + kaynak + lisans | 5 dk |
| 2 | §3.4/§3.5'ten hedef K katmanını doğrula | K etiketi | 2 dk |
| 3 | Lisans kapısını uygula (§4.4) | ✅/⚠️/❌ kararı | 2 dk |
| 4 | SOMUT girişi belirle: katman dosyası mı, ADR mi | Giriş noktası | 5 dk |
| 5 | Guardrail #14: mimari karar ise kullanıcı onayı | Onay | değişken |
| 6 | Katman dosyasına işle veya ADR aç | Vault güncellemesi | 10 dk |
| 7 | log.md append + bu indeksin envanterini güncelle | Audit trail | 2 dk |

~~~text
EKOSİSTEM DOSYASI OKU → K KATMANI EŞLE → LİSANS KAPISI → SOMUT GİRİŞ (katman dosyası | ADR) → ONAY (gerekirse) → İŞLE → LOG
~~~

### §5.2 Dosya Bazlı Kullanım Senaryoları

| Senaryo | Okunan Dosya | Beklenen Çıktı |
|---------|--------------|----------------|
| "Koel'in playlist mantığını K8'e nasıl alırız?" | muzik-streaming-sunuculari.md | smart_query sözleşmesi + MIT lisans notu |
| "Audio Service'i JUCE ile kurabilir miyiz?" | ses-dsp-acik-kaynak.md | GPL kararı: ADR veya alternatif kütüphane |
| "XU316 referans tasarımını K1'e nasıl işleriz?" | donanım-devre-referanslari.md | fark analizi satırı (K1 xmos-xu316.md) |
| "Transcode pipeline nereden başlar?" | ekosistem-mimarileri.md | K15 audio-transcoding + K14 content-delivery |
| "ASIO SDK'yı dağıtabilir miyiz?" | asio-wasapi-rehber.md | ✅ evet + LICENSE kontrol listesi |

### §5.3 Güncelleme Protokolü (Yeni Ekosistem Verisi Geldiğinde)

| # | Kural |
|---|-------|
| 1 | Yeni veriye **kaynak + tarih** etiketi konur (ör. exa 2026-09-24). Etiketsiz veri girmez. |
| 2 | Yeni referans havuza önce [[../architecture/github-referanslari]] §1-§5'e, sonra bu dizindeki ilgili dosyanın ilgili tablosuna eklenir (§8.2 havuz bütünlüğü). |
| 3 | Yeni K katmanı eşlemesi varsa hem bu indeksin §3.4'ü hem github-referanslari §6'sı güncellenir (iki tablo birlikte taşınır). |
| 4 | Eski satır **silinmez**, üstü çizilmez — değişiklik geçmişi append-only'dir. |
| 5 | Lisans değişikliği (fork, relicensing) tespit edilirse dosyanın §4.4'ü + bu indeksin §4.4'ü birlikte güncellenir. |

### §5.4 Çapraz Okuma Akışları

| Rol | Akış |
|-----|------|
| Backend | index.md §3.5 → muzik-streaming → ekosistem-mimarileri → k8-servis/*, k9-api-routing/* |
| Embedded | index.md §3.5 → ses-dsp → asio-wasapi → k2-surucu/*, k3-ses-motoru/* |
| Audio HW | index.md §3.5 → donanım-devre → k1-donanim/*, k16-k20/* |
| Windows SW | asio-wasapi → k2-surucu/wasapi-exclusive.md, asio-drivers.md → k0-isletim-sistemi/windows-api.md |
| MO / Vault Steward | index.md tamamı → §3.8 riskler → §6 doğrulama |

---

