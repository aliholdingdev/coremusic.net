---
type: architecture
category: layer
title: "K013 — CI/CD"
date: 2026-10-09
status: active
version: 2.0.1
authority: SSOT
---

# K013 — CI/CD

## §1 Kimlik
- Katman: K013 · Alan: **A4** (K12-K15).
- Kapsam: CI/CD — GitHub Actions, secret taraması, kalite kapıları, teslim.

## §2 Sorumluluk
1. `ci.yml` ana pipeline (build/test denetimi).
2. `secret-scan.yml` + `.gitleaks.toml` (GitLeaks) sızıntı taraması.
3. Vault gate'leri: LINT/şablon/ölçüm script'leri repo KÖKÜNDEN çalışır (AGENTS §13.6).
4. Deploy/rollback ve ortam matrisi (devops-engineer).
5. Modül sınırı denetiminin CI'a taşınması (plan §6 Faz 1 — mimari onay kapısı).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K000 (runner/altyapı).
- **Üst:** cross-cutting — K001–K020 tüm katmanların teslimi (bağımlılık yalnız alt zemine).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-082 | Dev/Staging Environment Architecture |

CI'ya özel ADR: **YOK** (`.ai/.decisions/index.md`'de pipeline başlıklı ADR bulunmadı) → **UNKNOWN**.

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `.github/workflows/{ci.yml,secret-scan.yml}` (2 dosya) · `.gitleaks.toml` kökte.

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: CI'nin geçtiği doğrulanamadı — GitHub Actions çalışma geçmişi diskte yok (AGENTS §25.2 aynı kaydı taşır).
- Workflow tetikleyicileri/izin matrisi okunmadı → **UNKNOWN**.
## §7 Makro Katman Karşılığı
K013-cicd → Makro **cross-cutting — K0–K4'e girmez** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K013, makro K0–K4 hiyerarşisinin **dışındadır**: K001–K020 tüm katmanların teslimini keser; bağımlılığı yalnız en dip zemine (K000 runner/altyapı) indirgenir (makro özet §6.1; matris §2: "K012 · K013 (cross-cutting) → K000 · tüm katmanları kapsar").
- Kardeş cross-cutting: K012 izleme. İkisi için de **CI/log'a özel ADR YOK** → politika UNKNOWN (makro özet §6.1, §7-13).
- Bu katman makro gruplamaya girmez; buna karşılık mimari denetim (modül sınırı, plan §6 Faz 1) CI kapısına taşınması PLANNED bir karardır (§2.5).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi:** git push/PR tetiklemeleri + repo içerikleri (workflow tetikleyicileri bu görevde okunmadı → UNKNOWN, §6).
- **Çıktı:** build/test denetim sonucu · secret tarama raporu · (PLANNED) mimari onay kapısı — teslim/deploy (§2.4 deploy/rollback + ortam matrisi, devops-engineer sorumluluğu).
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| Ana pipeline | `.github/workflows/ci.yml` | Build/test denetimi |
| Secret taraması | `.github/workflows/secret-scan.yml` | Sızıntı taraması workflow'u |
| GitLeaks yapılandırması | `.gitleaks.toml` (repo kökü) | Tarama kural seti |
| Vault gate betikleri | `.ai/scripts/*.ps1` — repo KÖKÜNDEN çalışır (AGENTS §13.6) | LINT/şablon/ölçüm kapıları |
| Ortam kararı | ADR-082 (Dev/Staging Environment Architecture) | K013 ADR bağlantısı (§4) |

- **İzinli/yasaklı çağrı özeti:** K013 → yalnız K000 (runner zemini) (matris §2); workflow'lar katman koduna **import etmez** — yalnız çalıştırır/değerlendirir. Vault gate betikleri `.ai/` içinden değil repo **kökünden** çalıştırılır (AGENTS §13.6 — yanlış yerde çalıştırma sahte "PNG yok" üretir). CI'ya özel ADR YOK → uzun vadeli sözleşme UNKNOWN (§4).
- Workflow tetikleyicileri/izin matrisi, job adımları okunmadı → **UNKNOWN** (§6; uydurma job adı yazılmaz).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **CI geçmişi doğrulanamadı** — GitHub Actions çalışma geçmişi diskte yok → pipeline'ın geçtiği bilinmiyor (§6; AGENTS §25.2 aynı kaydı taşır; makro özet §7-13).
  2. **CI'ya özel ADR YOK** — pipeline politikası belgelenmemiş (§4; makro özet §6.1).
  3. **Tetikleyici/izin matrisi okunmadı** → hangi olayın neyi tetiklediği UNKNOWN (§6) — gizli risk: yanlış tetiklemeyle deploy/rollback.
  4. **Modül sınırı denetimi CI'a taşınmadı** (§2.5 PLANNED, plan §6 Faz 1) → mimari ihlal (controller→PDO vb.) CI'da yakalanamaz.
- **İzolasyon:** secret-scan.yml + .gitleaks.toml sızıntıyı teslim öncesi yakalar; Vault gate'leri doküman bozulmasını commit öncesi yakalar (AGENTS §13.6/§13.7 kapıları). Hata CI'da kalır, çalışma ağacına kod olarak sızmaz.
- **Güvenlik sınırı (kim neyi doğrular):** sızıntı kapısı = `secret-scan.yml` + `.gitleaks.toml` (GitLeaks, §2.2); kalite kapısı = `ci.yml` (build/test, §2.1) + vault gate script'leri (AGENTS §13.6). Yetki/secret üretimi bu katmanın işi değil — `.env.figma`/REDACTED kuralı Security ve AGENTS §3'tedir; CI'da basılı secret yasaklığı `.gitleaks.toml` kapsamındadır (içerik bu görevde okunmadı → kapsam detayı UNKNOWN).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| CI'nin geçtiği (run geçmişi) | ⚠️ VERIFICATION REQUIRED — GitHub Actions geçmişi diskte yok (§6; makro özet §7-13) |
| Workflow tetikleyicileri/izin matrisi | UNKNOWN — okunmadı (§6) |
| CI'ya özel ADR | YOK — `.ai/.decisions/index.md`'de pipeline başlıklı ADR bulunmadı (§4) |
| `.gitleaks.toml` kural içeriği (kapsamı) | UNKNOWN — bu görevde okunmadı |
| Modül sınırı denetiminin CI'a taşınması (plan §6 Faz 1) | PLANNED — mekanizma yok |
