---
title: "K020-licence — Bağımlılık Lisans Kaydı (Dependency Licenses)"
type: register
category: architecture
node_id: K020-licence/dependency-licenses
node_type: file
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kayıt kuralı: her satır = resmi kaynak URL + tarih; kanıtsız satır yazılmaz"
related:
  - "license-verification.md"
  - "../K020-comilance/compliance-master.md"
  - "../../.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md"
group: kanit
---


# K020-licence — Bağımlılık Lisans Kaydı

## 1. Rol (ELI15)

Her bağımlılığın **lisansını, kaynağını ve tarihini** gösteren tek tablodur.
"Şu kütüphaneyi kullanıyoruz ama hangi izinle?" sorusu buradan cevaplanır.

## 2. Kayıt kuralı (sütunlar zorunlu)

```text
| # | Bağımlılık | Kaynak (resmi URL) | Lisans | Kayıt tarihi | Projeye etkisi | Durum |
```

- **Lisans iddiası** yalnız resmi sayfadan yazılır (satır içi link zorunlu).
- **Etki** alanı `⚠️ VERIFY REQUIRED` ile başlar — lisans hukuki etkisi **bu dosyada
  karara bağlanmaz**, ADR'ye taşınır (`K020-adr → .ai/.decisions/draft/`).
- Kanıtsız satır eklenmez; eski satır düzeltilmez, yeni satır + `⚠️` ile eklenir.

## 3. Kayıtlar (açık bağımlılıklar)

| # | Bağımlılık | Kaynak | Lisans | Kayıt tarihi | Projeye etkisi | Durum |
|---|---|---|---|---|---|---|
| 1 | **Steinberg ASIO SDK** | https://www.steinberg.net/developers/asiosdk-open | **GPLv3** (açık kaynak olarak yayınlandı) | 2026-10-12 | `⚠️ VERIFY REQUIRED: projeye etkisi` — CoreMusic'in dağıtım modeli (açık kaynak / kapalı) ile GPLv3 etkileşimi **değerlendirilmedi** (ADR-097 §6/5) | açık |

> **Bulgunun kaynağı:** 2026-10-12 web doğrulama tur-1 — Steinberg, ASIO SDK'yi
> açık kaynak (GPLv3) olarak duyurdu (ADR-097 §1.6 aynı bulguyu kaydeder).

## 4. Referans proje tablosu (lisans karşılaştırması — BAĞIMLILIK DEĞİL)

Bu satırlar CoreMusic'in **bağımlılığı değildir**; yalnızca sektördeki benzer
projelerin lisans tercihini karşılaştırmak için tutulur (kaynak: 2026-10-12 web tur-1).

| Proje | Yıldız | Lisans | Not |
|---|---|---|---|
| **Koel** | 17.175★ | **MIT** | Laravel tabanlı müzik sunucusu |
| **Ampache** | 3.793★ | **AGPL-3.0** | copyleft — dağıtımda kaynak paylaşımı şartı |
| **Jellyfin** | 55.732★ | **GPL-2.0** | medya sunucusu, copyleft |

## 5. Kapsam dışı / henüz kaydedilmeyenler

| Öğe | Neden kayıtta değil | Durum |
|---|---|---|
| PHP · MySQL · Composer paketleri (ör. php-di, fast-route) | lisans envanteri çalıştırılmadı | `⚠️ VERIFY REQUIRED` |
| JUCE (ses motoru) | 2026-10-12 tur-1'de yıldız doğrulandı, **lisansı ayrıca doğrulanmadı** | `⚠️ VERIFY REQUIRED` |
| Frontend/CSS vendor dosyaları | envanter yok | `⚠️ VERIFY REQUIRED` |
| OS/bağlayıcı katmanı (WASAPI/ALSA vb.) | platform lisansları incelenmedi | `⚠️ VERIFY REQUIRED` |

## 6. Açık sorular

1. **ASIO SDK GPLv3** — CoreMusic'in lisans modeli ne? (GPLv3 bulaşıcılığı riski
   `⚠️ VERIFY REQUIRED` — karar ADR gerektirir)
2. Bağımlılık envanteri hangi araçla üretilecek? (ör. Composer/`licensee` — araç seçimi ONAY)
3. Lisans taraması CI'ya (`secret-scan.yml` yanında) eklenecek mi? → `⚠️ VERIFY REQUIRED`
4. ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## 9. Sürüm ve kanıt kaydı biçimi

Bir lisans satırı yalnız sürüm/kanıt çiftiyle **kalıcılaşır**:

```text
bağımlılık = ad + sürüm (ör. composer.lock'daki exact version)
kanıt      = dosya yolu (vendor/LICENSE) VEYA URL + tarih
durum      = açık | VERIFY REQUIRED | kapandı (ADR no ile)
```

- Sürüm **bilinmiyorsa** satır `VERIFY REQUIRED` kalır; "en son sürüm" diye tahmin yazılmaz.
- URL ölmüşse (404) yerine **sürüm etiketli arşiv linki** konur; ikisi de yoksa satır düşürülmez, `⚠️` ile işaretlenir.
- `composer.lock` / `package.json` **kilit dosyasıdır** — sürüm kaynağı onlardır, readme değil.

## 10. YASAK beyanlar (bu dosyada hiçbiri yazılmaz)

| Yasak cümle | Neden |
|---|---|
| "Tüm bağımlılıklar uyumlu/sorunsuz" | envanter çalıştırılmadı (§8) |
| "Lisans etkisi yok" | etki değerlendirmesi ADR işidir, bu dosya işi değil |
| "GPL bulaşıcıdır, kullanamayız" / "AGPL sorun değil" | **hukuki görüş** — kaynak yok |
| "JUCE MIT'dir" (veya başka lisans) | 2026-10-12 tur-1'de yalnız yıldız doğrulandı |
| Kaynağı olmayan sürüm numarası | uydurma veri (kök AGENTS.md §3) |

## 11. Bağımlılık → sorumlu eşlemesi

| Bağımlılık sınıfı | Kaydı açan | Onaylayan |
|---|---|---|
| PHP paketleri | `dependency-manager` | `backend-architect` |
| Frontend/CSS vendor | `dependency-manager` | `ui-designer` |
| Ses motoru bağımlıları (JUCE, ASIO SDK) | `dependency-manager` | `embedded-engineer` |
| Platform SDK (WASAPI/ALSA) | `dependency-manager` | `windows-software-engineer` |
| Yeni lisans ailesi (ör. AGPL) girişi | `dependency-manager` | `security-engineer` + **ADR** |


## 7. Lisans aileleri rehberi (genel bilgi — hukuki etki bu dosyada DEĞİL)

| Aile | Temel ilke | Örnek lisanslar | CoreMusic açısından not |
|---|---|---|---|
| **Permissive** | dağıtım serbest, atıf şart | MIT · BSD · Apache-2.0 | en düşük sürtünme |
| **Weak copyleft** | kütüphane/kapsül korumalı, kendiliğinden açık kaynak değil | LGPL · MPL · EPL | dinamik bağlama modeline bağlı → `⚠️ VERIFY REQUIRED` |
| **Strong copyleft** | dağıtılan çalışır iş aynı lisansla | GPL-2.0 · GPLv3 | dağıtım anında tetiklenir |
| **Network copyleft** | ağ üzerinden servis de tetikler | AGPL-3.0 | barındırılan servislerde risk — Ampache bu ailede |

> Bu tablo **lisans tanımı** içerir, **hukuki görüş içermez**. CoreMusic'e etkisi için
> karar `K020-adr` üzerinden ADR'ye gider (ör. ASIO GPLv3 — ADR-097 §6/5).

## 8. Envanter kaynakları (ne taranacak — envanter HENÜZ YAPILMADI)

| # | Kaynak | Ne bulur | Durum |
|---|---|---|---|
| 1 | `composer.json` / `composer.lock` (proje kökü) | PHP paketleri + `license` alanı | taranmadı → `⚠️ VERIFY REQUIRED` |
| 2 | `package.json` (frontend araç zinciri) | npm paketleri | taranmadı → `⚠️ VERIFY REQUIRED` |
| 3 | IDE/dahili vendor dizinleri | gömülü JS/CSS vendor'ları | taranmadı → `⚠️ VERIFY REQUIRED` |
| 4 | C++/DSP tarafı (JUCE, ASIO SDK, bağımlı header'lar) | motorsiklet/zincir lisansları | kısmi — yalnız ASIO kayıtlı |
| 5 | Platform/bağlayıcı (WASAPI, ALSA, driver SDK'ları) | OS dağıtım lisansları | taranmadı → `⚠️ VERIFY REQUIRED` |

**Kural:** envanter çalıştırılmadan "tüm bağımlılıklar uyumlu" cümlesi **yazılmaz.**

