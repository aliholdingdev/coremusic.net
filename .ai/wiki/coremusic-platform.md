---
title: "CoreMusic Platform (Çatı Proje)"
type: proje
created: 2026-10-06
updated: 2026-10-06
sources: [not-repo-envanteri, not-readme-vizyon]
tags: [platform, mimari, coremusic]
---

# CoreMusic Platform

Tek repository, 6 modüllü çok-altyapılı PHP platformu: `C:\www\coremusic.net`.

## Modüller

- [[shared-infrastructure]] — ortak PHP paketi (composer)
- [[home-coremusic-net]] · [[api-coremusic-net]] · [[auth-coremusic-net]] · [[media-coremusic-net]] · [[assets-coremusic-net]]

## Ortak Omurga

- Runtime: [[php-8-4]] + [[pdo]] + [[apcu]]
- Araçlar: [[composer]] · [[phpunit]] · [[phpstan]] · [[playwright]] · [[vitest]]

## README Göre Mimarî (v2.0.1)

- 21 katman, 1095 bileşen; K5 Veri 55 bileşen ([[mysql-9]]), K3 Ses 55 bileşen ([[neva-engine]]), K11 UX 45 bileşen ([[vanilla-javascript]])
- Lisans: Proprietary · Authority: [[bayram-ali]] (Vault Steward)

⚠ **Çelişki (✅ çözüldü 2026-10-06 18:37):** README `pro.coremusic.net` (Donanım & DSP Paneli) alt alan adını anlatıyor; diskte `pro.coremusic.net` YOK. **Çözüm:** README'nin alt alan adı tablosu (10 satır: music, home, car, studio, download, media, admin, auth, pro, coremusic.net) bir **vizyon/plan** listesidir; disk **implementasyondur**. Kanıt-1: `README.md:83-92` (plan) · Kanıt-2: disk `dir` → 5 mevcut (api, assets, auth, home, media). Kapsam farkı: diskteki `api.coremusic.net` ve `assets.coremusic.net` README plan tablosunda yer almıyor. İkisi de kendi bağlamında doğru → gerçek bilgi çelişkisi değil, plan≠implementasyon farkı. Kaynaklar: `.ai/raw/not-readme-vizyon.md` + `.ai/raw/not-repo-envanteri.md`.

Kaynak: `.ai/raw/not-repo-envanteri.md` · `.ai/raw/not-readme-vizyon.md`

## İlgili Sayfalar
- [[shared-infrastructure]] — çekirdek paket
- [[neva-engine]] · [[mysql-9]] · [[vanilla-javascript]] — teknoloji katmanları
- [[bayram-ali]] — otorite
