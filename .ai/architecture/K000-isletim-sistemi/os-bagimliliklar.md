---
title: "K000-isletim-sistemi — Bağımlılıklar"
type: architecture-dependencies
node_id: K000-isletim-sistemi
status: draft
date: 2026-10-12
updated: 2026-10-12
---

# os-bagimliliklar — K000 Bağımlılık Matrisi

## §1 Etiket kuralı (önce oku)

| Etiket | Anlamı |
|---|---|
| **[TASARIM]** | Bu satır bir **tasarım iddiasıdır** — disk kanıtı yoktur, mimari niyeti yansıtır |
| **[KANIT]** | Dosya yolu / URL ile kanıtlanmış satır |
| **[BİLİNMEYEN]** | Tahmin edilmedi → ⚠️ VERIFICATION REQUIRED |

> Aşağıdaki matrisin tamamı **[TASARIM]** etiketlidir; K000 bu turda kod olarak
> uygulanmamıştır. Kanıt taşıyan tek satır §3'tedir.

## §2 depends_on / used_by matrisi

| Düğüm | Yön | Etiket | Gerekçe |
|---|---|---|---|
| K000-isletim-sistemi | → (hiçbirine) | [TASARIM] | Zemin katman; alt katmanlara bağımlı olmaması beklenir (Dependency Rule: dışa doğru tek yön) |
| K003-audio | → K000 | [TASARIM] | Ses motoru OS ses API'sine (WASAPI/ALSA/Core Audio) bağlanır |
| K004-dsp | → K000 | [TASARIM] | DSP zinciri OS zamanlayıcı ve bellek hizmetlerine ihtiyaç duyar |
| K005-firmware | → K000 | [TASARIM] | Firmware güncelleme/diagnostik akışı OS üzerinden yapılır |
| K014-network | → K000 | [TASARIM] | Soket/yetki katmanı OS'e aittir |
| K018-devops | → K000 | [TASARIM] | Build/çalıştırma ortamı OS sürümüne bağlıdır |
| K000 | → K001-donanım | [TASARIM] | OS, donanımı sürücü üzerinden görür (sınır sürücü arayüzüdür) |
| K000 | → (geri dönüş) | [TASARIM] | K001'in K000'e geri bağımlılığı **yoktur** — firmware ve donanım, OS'ten bağımsızdır |

### §2.1 Özet

- **Gelen (used_by):** 5 tasarım iddiası (K003 · K004 · K005 · K014 · K018)
- **Giden (depends_on):** 1 tasarım iddiası (K001 — sürücü sınırı)
- **Kanıtlanmış:** 0 → hepsi [TASARIM]

## §3 Entegrasyon sınırları

| Sınır | Ne taşır | Etiket |
|---|---|---|
| OS ↔ sürücü | Cihaz açma/kapama, buffer aktarımı | [TASARIM] |
| OS ses alt sistemi ↔ K003 | Ortak (shared) / münhasır (exclusive) akış | [KANIT] — MS Learn Exclusive-Mode Streams, 2026-10-12 |
| OS ↔ uygulama | Syscall / API yüzeyi (isletim-sistemi-api) | [TASARIM] |

**Kritik entegrasyon uyarısı (kanıtlı):** WASAPI exclusive mode, ses uç noktasını ele geçirir;
diğer tüm uygulama/sistem sesleri susar. CoreMusic böyle bir yola girerse **kullanıcıya
görünür bir shared-mode dönüş seçeneği** sunmalı ve cihazı odak kaybında bırakmalıdır
(kaynak: Microsoft Learn, 2026-10-12).

## §4 Riskli bağımlılıklar

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | Platform seti bilinmiyor (Windows-only mı, çapraz mı?) | K000 çocuk kapsamının tamamı belirsiz | ⚠️ üst karar — os-dogrulama.md Q4 |
| 2 | core / kernel kapsam çakışması | Aynı iş iki yerde tanımlanabilir | ⚠️ kapsam ayrımı onayı |
| 3 | electroncis tanımsız | K000 ↔ K001 sınırında boşluk | ⚠️ kapsam onayı |
| 4 | Exclusive-mode kullanımı | Sistem seslerini bastırma UX riski | K003 entegrasyonunda user-visible fallback |
| 5 | K002 ID GAP | Numara sırası/kayıt defteri tutarsızlığı | ⚠️ registry §6 kaydı korunur |

## §5 Kapanış

- Bu dosya **salt tasarım kaydıdır**; status: draft.
- Kanıtlanmış bağımlılık eklenince satır [TASARIM] → [KANIT] olarak güncellenir ve
  os-dogrulama.md §1'e kayıt düşülür.

## §6 Bağımlılık ölçüm özeti (2026-10-12)

| Metrik | Değer |
|---|---|
| Matris satırı (§2) | 8 |
| [TASARIM] etiketli satır | 8 |
| [KANIT] etiketli satır | 1 (yalnız §3'teki exclusive-mode satırı) |
| [BİLİNMEYEN] etiketli satır | 0 |
| Kırılgan / yüksek riskli bağımlılık | 3 (§4: 1 · 2 · 3) |
| Gelen bağımlılık (used_by iddiası) | 5 |
| Giden bağımlılık (depends_on iddiası) | 1 |

**Geçiş kuralı:** herhangi bir satır dosya yolu veya URL ile kanıtlanınca etiketi
[TASARIM] → [KANIT] olur ve os-dogrulama.md §1'e satır olarak düşülür.
Etiketsiz satır bu dosyada geçerli sayılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
