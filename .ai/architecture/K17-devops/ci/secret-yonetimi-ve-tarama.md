---
title: "K17-devops/ci — Secret Yönetimi ve Tarama: .env Şablonu, GitLeaks Kapısı ve Sızıntı Yanıtı"
node_id: K17-devops/ci/secret-yonetimi-ve-tarama
node_type: module
status: draft
parent_id: K17-devops/ci
children: []
depends_on:
  - K17-devops
  - K17-devops/ci
evidence:
  - ".github/workflows/secret-scan.yml — 31 satir tam okuma: Satır 4 (.gitleaks.toml YOK), Satır 26 (fetch-depth 0), Satır 28-31 (gitleaks-action@v2 + GITHUB_TOKEN) (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 14-15 permissions contents: read (asgari yetki) (2026-10-12)"
  - ".gitignore — Satır 68-71 Figma token satirlari (.ai/.env.figma, .ai/.env.*, !.env.example) (2026-10-12)"
  - "shared/config/.env.example — 105 satir; Satır 5 'NEVER commit .env', Satır 27 DB_PASSWORD bos, Satır 60-92 OAuth client secret bos, Satır 100-102 JWT key yollari (2026-10-12)"
  - "api.coremusic.net/.env.example — Satır 5 'GERCEK SECRET YAZMA', Satır 15 DB_HOST_PLACEHOLDER (2026-10-12)"
  - "auth.coremusic.net/config/.env.example — Satır 8-9 DB_USER/DB_PASSWORD bos (2026-10-12)"
  - "shared/src/Log/Redactor.java yerine Redactor.php — Satır 28 MASK, Satır 31 SENSITIVE_KEYS, Satır 40-65 redaction ornekleri (2026-10-12)"
validation:
  - "Dosya satir sayilari [IO.File]::ReadAllLines ile olcüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa basligi ile dondu (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada cakisma 0 (2026-10-12)"
  - ".env.example x3 ve secret-scan.yml tam okundu; satirlar birebir dogrulandi (2026-10-12)"
open_questions:
  - "Gitleaks son calisma sonucu (CI run) depoda degil — gecti mi bilinmiyor (⚠️ VERIFICATION REQUIRED)"
  - ".gitleaks.toml yok (varsayilan config); kural genisletme karari verilmedi (⚠️ VERIFICATION REQUIRED)"
  - "Depodaki gercek .env dosyasi sayisi: 0 (yalnizca .env.example x3) — dogrulandi, uretim sirri ortam degiskeni mi bilinmiyor (UNKNOWN)"
  - "Secret rotation (donusum) periyodu tanimli degil (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K17-devops/ci/secret-yonetimi-ve-tarama

> **ELI10:** Secret (sır), erişimi olan herkesin zarar verebileceği bilgidir: parola, API
> anahtarı, özel anahtar. Bu dosya üç şeyi kurar: secret **nasıl saklanır** (.env),
> secret depoya **nasız girmez** (GitLeaks kapısı) ve secret **sızarsa ne yapılır** (yanıt).
> Bu depoda gerçek secret değerleri yoktur — yalnızca **boş şablonlar** vardır.

## §1 Amaç

Bu modül, CoreMusic'in **CI/CD secret yönetim kurallarının tek yetkili tanımıdır**. Beş karar bu
dosyada verilir: şablon biçimi · tarama kapısı · Actions kullanımı · yasaklar · sızıntı yanıtı.
Güvenlik **politikası** K009-security'nin yetkisindedir; burada DevOps yüzeyi (workflow + şablon)
tanımlanır (K018/index.md Satır 19).

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Secret nasıl saklanır? | `.env` şablon kuralı | §7 |
| 2 | Depoya secret nasıl girmez? | GitLeaks kapısı | §5 |
| 3 | CI'da secret nasıl kullanılır? | Actions kuralı | §6 |
| 4 | Hangi secret sınıfları var? | Envanter | §8 |
| 5 | Ne kesinlikle yasak? | Yasaklar | §9 |
| 6 | Sızarsa ne yapılır? | Olay yanıtı | §15 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Tarama kapısı | job | `gitleaks` (secret-scan.yml Satır 19) | mevcut |
| H2 | `.gitleaks.toml` | dosya | 0 (varsayılan config) | Satır 4 yorumu |
| H3 | Şablon dosyası | adet | 3 (`.env.example`) | glob 2026-10-12 |
| H4 | Commit edilmiş gerçek `.env` | adet | 0 | glob 2026-10-12 |
| H5 | Workflow yetkisi | ayar | `contents: read` | ci.yml 14-15, scan 11-12 |
| H6 | Sızıntı yanıtı | playbook | tanımsız | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Şablon | ✅ `.env.example` biçimi ve kuralları | ❌ Uygulama kodu okuması → varlık kodu |
| Tarama | ✅ GitLeaks kapısı kuralları | ❌ Güvenlik politikası → K009-security |
| Actions | ✅ `secrets.*` kullanım kuralı | ❌ Workflow tasarımı → `ci/` subdomaini |
| Log redaction | ✅ İş birliği sınırı (Redactor) | ❌ Redaction kural seti → K16-observability |
| Üretim değeri | ✅ Yokluk gerçeği | ❌ Sır üretimi → insan süreci |
| Rotation | ✅ Kural taslağı | ❌ Periyot kararı → ⚠️ VERIFICATION REQUIRED |
| Container secret | ✅ Sınır (build `ARG` yasak) | ❌ İmaj kuralları → `artifact-management/` |

**Sahiplik kararı:** secret-scan iş birliği Security (kapsam) + DevOps (yml) şeklindedir
(devops-master.md Satır 26, 30).

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Secret | secret | Gizli değer | §8 |
| Şablon | template | Boş, versiyonlanabilir örnek | `.env.example` |
| Placeholder | placeholder | Gerçekçi olmayan yer tutucu | Satır 15 (api) |
| `.env` | env file | Yerel değer dosyası | .gitignore Satır 69-70 |
| GitLeaks | gitleaks | Secret tarama aracı | scan Satır 29 |
| Kapı | gate | Kırmızı yapan CI işi | §5.1 |
| GITHUB_TOKEN | GITHUB_TOKEN | Actions otomatik token'ı | scan Satır 31 |
| Yetki | permission | Workflow yetki seti | ci.yml Satır 14-15 |
| Client secret | client secret | OAuth gizli anahtarı | env.example Satır 60-92 |
| Özel anahtar | private key | JWT imzalama anahtarı | env.example Satır 100-102 |
| Redaction | redaction | Logda maskeleme | Redactor.php Satır 28 |
| Rotation | rotation | Secret yenileme | §15.4 |
| fetch-depth | fetch-depth | Git geçmişi derinliği | scan Satır 26 |

## §4 Repo gerçeği — secret envanteri

### §4.1 Tarama workflow'u (secret-scan.yml tam okuma)

| # | Satır | İçerik | Anlamı |
|---|---|---|---|
| 1 | 4 | `.gitleaks.toml` diskte yok → varsayılan config kullanılır | Özel config yok |
| 2 | 7-9 | `on: push / pull_request` | Her değişiklikte tarama |
| 3 | 11-12 | `permissions: contents: read` | Asgari yetki |
| 4 | 14-16 | `concurrency` + `cancel-in-progress` | Eski koşu iptal |
| 5 | 19-22 | `gitleaks` job, `ubuntu-latest`, 10 dk | Tek kapı |
| 6 | 26 | `fetch-depth: 0` | Tam geçmişi tara |
| 7 | 28-31 | `gitleaks/gitleaks-action@v2` + `GITHUB_TOKEN` | Çalıştırma |

### §4.2 Ortam dosyası envanteri (glob 2026-10-12)

| # | Dosya | Satır | Durum | Not |
|---|---|---|---|---|
| 1 | shared/config/.env.example | 105 | Şablon | Master şablon |
| 2 | auth.coremusic.net/config/.env.example | 42 | Şablon | Boş değerler |
| 3 | api.coremusic.net/.env.example | 53 | Şablon | Placeholder |
| 4 | Gerçek `.env` | 0 | Yok (glob) | ⚠️ Üretim değeri bilinmiyor |
| 5 | `.ai/.env.figma` | — | Gitignore'da | .gitignore Satır 69 |
| 6 | `.ai/.env.*` | — | Gitignore'da | .gitignore Satır 70 |
| 7 | `!.env.example` | — | İstisna (commit edilir) | .gitignore Satır 71 |

### §4.3 Şablon içeriği — değer yok, anahtar var

| # | Sınıf | Şablon satırı | Değer durumu | Kural |
|---|---|---|---|---|
| 1 | Uygulama | api .env.example 9-12 | Sabit (güvenli) | Versiyonlanabilir |
| 2 | Veritabanı | auth 8-9 · shared 27 | **Boş** | §7.2 |
| 3 | Oturum | auth 13-15 | Güvenli değer | Versiyonlanabilir |
| 4 | OAuth client secret | shared 60-92 | **Boş** | §7.2 |
| 5 | JWT key yolu | shared 100-102 | Yol (değer değil) | §7.4 |
| 6 | CSRF uzunluğu | shared 38 | Ayar | Versiyonlanabilir |
| 7 | Figma token | .gitignore 68 | Gitignore | Yerel |

### §4.4 Şablon yorumları — depo-kendi-kuralı

| # | Dosya / Satır | Yorum metni (özet) | Kural seviyesi |
|---|---|---|---|
| 1 | shared/.env.example / 5 | `.env` dosyaları VCS'e commit edilmez | Zorunlu |
| 2 | api/.env.example / 5 | Gerçek secret yazma; bu dosya şablondur | Zorunlu |
| 3 | api/.env.example / 15 | `DB_HOST=DB_HOST_PLACEHOLDER` | Placeholder kalıbı |
| 4 | shared/.env.example / 100 | `private.pem` gitignore'dadır; `openssl genpkey` ile üretilir | Anahtar üretimi |
| 5 | secret-scan.yml / 4 | `.gitleaks.toml` yok → varsayılan config | Bilgi |

## §5 GitLeaks kapısı kuralları

### §5.1 Kapı tanımı

| # | Alan | Mevcut değer | Kural | Neden |
|---|---|---|---|---|
| 1 | Tetikleyici | `push` + `pull_request` | Zorunlu | Erken yakalama |
| 2 | Geçmiş derinliği | `fetch-depth: 0` | Zorunlu | Tarih boyu tarama |
| 3 | Config | Varsayılan (`.gitleaks.toml` yok) | Bilinçli | Satır 4 yorumu |
| 4 | Yetki | `contents: read` | Zorunlu | Asgari |
| 5 | Bütçe | 10 dk | Sabit | scan Satır 22 |
| 6 | Sonuç | Kırmızı job | Kapı etkisi | ⚠️ Sonuç raporu depoda yok |

### §5.2 Config kararı matrisi

| # | Durum | Seçenek | Artı | Eksi | Karar |
|---|---|---|---|---|---|
| 1 | Şu an | Varsayılan config | Bakım yok | Özelleşme yok | Kabul (Satır 4) |
| 2 | Özel config | `.gitleaks.toml` | Kural genişletme | Bakım | ⚠️ VERIFICATION REQUIRED |
| 3 | Allowlist | Config içinde | Sahte pozitif azaltma | Gözden kaç riski | Politika K009 |
| 4 | Yeni kural | ADR + onay | Denetim | Yavaş | Kural |

### §5.3 Kapı ile diğer kapıların ilişkisi

| # | Kapı | Dosya | Satır | Sertlik |
|---|---|---|---|---|
| 1 | Secret tarama | secret-scan.yml | 19-31 | Sert (varsayılan) |
| 2 | PHP lint | ci.yml | 25-28 | Sert |
| 3 | PHP test | ci.yml | 67-80 | Sert |
| 4 | JS kapsam | ci.yml | 160-167 | Soft |
| 5 | E2E | ci.yml | 178-183 | Soft |

**Kural:** secret taraması **asla soft yapılamaz** — sızan secret'ın PR'ı geçmesi geri
dönülemez hasar üretir (§15).

### §5.4 Tarama kapsamı kuralları

| # | Kapsam | Zorunlu mu | Gerekçe | Not |
|---|---|---|---|---|
| 1 | Tam git geçmişi | evet | Eski sızıntılar | `fetch-depth: 0` |
| 2 | Tüm branch'ler | evet | Dal bazlı sızıntı | Checkout varsayılanı |
| 3 | Commit mesajları | tercih | Mesaj sızıntısı | ⚠️ VERIFICATION REQUIRED |
| 4 | Artefaktlar | tercih | İkincil sızıntı | ⚠️ VERIFICATION REQUIRED |

## §6 GitHub Actions secret kullanımı

### §6.1 Kural tablosu

| # | Kural | Zorunlu mu | Repo kanıtı | Gerekçe |
|---|---|---|---|---|
| 1 | `secrets.*` yalnız `env:` bloğunda | evet | scan Satır 30-31 | Görünürlük |
| 2 | Workflow düzeyinde `permissions` | evet | ci.yml 14-15, scan 11-12 | Asgari yetki |
| 3 | `GITHUB_TOKEN` varsayılan yeterli | tercih | scan Satır 31 | Ek token yok |
| 4 | PR'dan fork secret akışı | kısıtlı | ⚠️ Doğrulanmadı | Sızıntı riski |
| 5 | Log'a secret basma | yasak | §9 | Maskelenmez |
| 6 | Üçüncü parti action pinleme | tercih | `@v4`, `@v2` | Sürüm kilidi |

### §6.2 Yetki (permission) matrisi

| # | Workflow | Yetki | Satır | Yorum |
|---|---|---|---|---|
| 1 | ci.yml | `contents: read` | 14-15 | Yazma yok |
| 2 | secret-scan.yml | `contents: read` | 11-12 | Yazma yok |
| 3 | Kural | Yeni workflow'ta `permissions` eksik olmaz | — | §6.1 satır 2 |
| 4 | İstisna | İstisna gerekçesi yazılır | — | Denetim |

### §6.3 Secret – job eşlemesi (mevcut)

| # | Job | Secret | Satır | Kaynak |
|---|---|---|---|---|
| 1 | `gitleaks` | `secrets.GITHUB_TOKEN` | 31 | Otomatik |
| 2 | `php-test` | — | 67-108 | Secret yok |
| 3 | `js-test` | — | 142-158 | Secret yok |
| 4 | `js-e2e` | — | 178-203 | Secret yok |
| 5 | Sonuç | CI'da tanımlı ek secret **yok** | — | 2026-10-12 okuma |

## §7 `.env` şablon kuralları

### §7.1 Şablon biçimi

| # | Kural | Zorunlu mu | Kanıt | Gerekçe |
|---|---|---|---|---|
| 1 | Şablon commit edilir | evet | `!.env.example` (.gitignore 71) | Paylaşım |
| 2 | Gerçek değer yasak | evet | api .env.example 5 | Sızıntı |
| 3 | Her alt alan için şablon | evet | 3 dosya (§4.2) | Yerellik |
| 4 | Şablon = anahtar listesi | evet | §4.3 | Sözleşme |
| 5 | Yorum satırı açıklama | tercih | shared .env.example | Okunabilirlik |

### §7.2 Değer kuralı

| # | Değer türü | Şablonda | Üretimde | Denetim |
|---|---|---|---|---|
| 1 | Secret (parola/anahtar) | **Boş** | Ortam değişkeni | Şablon taraması |
| 2 | Placeholder | Gerçekçi-değil | Gerçek değer | `PLACEHOLDER` dizesi |
| 3 | Ayar (timezone vb.) | Gerçek | Gerçek | — |
| 4 | Yol | Yol yazılır | Ortama göre | §7.4 |
| 5 | Enum (mod) | Tüm seçenekler | Tek seçim | Yorum |

### §7.3 Dosya–değer eşlemesi

| # | Dosya | Kapsam | Secret satırı | Durum |
|---|---|---|---|---|
| 1 | shared/config/.env.example | Master | 27, 60-92 | Boş (doğrulandı) |
| 2 | auth.coremusic.net/config/.env.example | Auth | 8-9 | Boş (doğrulandı) |
| 3 | api.coremusic.net/.env.example | API | 15 (placeholder) | Placeholder |
| 4 | Kural | Her şablon | Boş/placeholder | §7.2 |

### §7.4 Özel anahtar (JWT) kuralı

| # | Kural | Kanıt | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | `private.pem` gitignore'da | env.example Satır 100 | Anahtar commit edilmez | .gitignore taraması |
| 2 | Üretim `openssl genpkey` | Satır 100 yorumu | İnsan üretimi | Prosedür |
| 3 | Şablonda yalnız **yol** | Satır 101-102 | Değer değil | Şablon |
| 4 | Yol anahtarı sabit | Şablonda | Sözleşme | Kod okuma |
| 5 | Anahtar dönüşümü | Tanımsız | — | ⚠️ VERIFICATION REQUIRED |

## §8 Secret sınıfı envanteri

| # | Sınıf | Örnek anahtar | Şablonda | Değer | Üretim kaynağı |
|---|---|---|---|---|---|
| 1 | Veritabanı | `DB_PASSWORD` | evet | boş | Ortam değişkeni |
| 2 | OAuth client secret | `OAUTH_*_CLIENT_SECRET` | evet (11 kayıt) | boş | Sağlayıcı paneli |
| 3 | JWT anahtarı | `JWT_PRIVATE_KEY_PATH` | evet (yol) | değer yok | `openssl genpkey` |
| 4 | Figma token | `.ai/.env.figma` | hayır | gitignore | İnsan |
| 5 | CI token | `GITHUB_TOKEN` | hayır | otomatik | GitHub |
| 6 | CSRF ayarı | `CSRF_TOKEN_LENGTH` | evet | ayar (32) | Şablon |

### §8.1 Envanter kuralları

| # | Kural | Zorunlu mu | Denetim | Gerekçe |
|---|---|---|---|---|
| 1 | Her secret sınıfı şablonda görünür (anahtar) | evet | Şablon okuması | Sözleşme |
| 2 | Hiçbir secret sınıfının değeri şablonda yok | evet | §7.2 | Sızıntı |
| 3 | Yeni secret = şablon + gitignore + tarama | evet | §5 | Bütünlük |
| 4 | Kullanılmayan secret silinir | tercih | Denetim | Gürültü |
| 5 | Envanter env'de tek kaynak | evet | `.env` | §7.1 |

## §9 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Gerçek secret'ı `.env.example`'a yazmak | Commit edilir | Şablon incelemesi |
| 2 | `.env` dosyasını commit etmek | Gitignore | .gitignore Satır 70 + tarama |
| 3 | Secret'ı `.md`/`.json`/`.log`a yazmak | Kalıcı sızıntı | Kod incelemesi |
| 4 | Secret'ı log'a basmak | Log okuyucusu görür | Redactor + §12 |
| 5 | Build `ARG` ile secret geçmek | İmaj katmanında kalır | Artifact dosyası §13 |
| 6 | Workflow'ta `permissions` eksik bırakmak | Aşırı yetki | §6.2 |
| 7 | Fork PR'a secret akışını açık bırakmak | Dış sızıntı | §6.1 satır 4 |
| 8 | tarama kapısını soft yapmak | Geri dönüşsüz hasar | §5.3 |
| 9 | Secret'ı paylaşmak (ekip dâhil) | Denetim kaybı | Süreç |
| 10 | `latest` action sürümü + secret | Sürpriz davranış | §6.1 satır 6 |

## §10 Kod örnekleri — repo gerçek kodu

### §10.1 Tarama workflow'u — secret-scan.yml Satır 11-12 ve 24-31

```yaml
permissions:
  contents: read
```

```yaml
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0   # github-actions-template.md §10: "gitleaks: no commits found" -> fetch-depth: 0

      - name: Run GitLeaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

**Dosya: `.github/workflows/secret-scan.yml` / Satır: 11 · 12 · 24 · 25 · 26 · 28 · 29 · 30 · 31**

### §10.2 Gitignore — Satır 68-71

```gitignore
# ---- Figma API token (local only) ----
.ai/.env.figma
.ai/.env.*
!.env.example
```

**Dosya: `.gitignore` / Satır: 68 · 69 · 70 · 71**

### §10.3 Şablon yorumu — api/.env.example Satır 4-6

```gitignore
# Kopyala: config/.env  (constants.php config/.env okur)
# GERÇEK SECRET YAZMA — bu dosya şablondur, versiyonlanabilir.
```

**Dosya: `api.coremusic.net/.env.example` / Satır: 4 · 5**

### §10.4 Asgari yetki — ci.yml Satır 14-15

```yaml
permissions:
  contents: read
```

**Dosya: `.github/workflows/ci.yml` / Satır: 14 · 15**

## §11 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | .github/workflows/secret-scan.yml | 4 | `.gitleaks.toml` yok yorumu |
| 2 | .github/workflows/secret-scan.yml | 7-9 | push + pull_request tetikleyici |
| 3 | .github/workflows/secret-scan.yml | 11-12 | `contents: read` |
| 4 | .github/workflows/secret-scan.yml | 14-16 | Concurrency + cancel |
| 5 | .github/workflows/secret-scan.yml | 19-22 | gitleaks job + 10 dk |
| 6 | .github/workflows/secret-scan.yml | 26 | `fetch-depth: 0` |
| 7 | .github/workflows/secret-scan.yml | 29-31 | gitleaks-action@v2 + token |
| 8 | .github/workflows/ci.yml | 14-15 | Asgari yetki |
| 9 | .gitignore | 68-71 | Token/`.env` yasakları + istisna |
| 10 | shared/config/.env.example | 5 | "NEVER commit .env" kuralı |
| 11 | shared/config/.env.example | 27 | `DB_PASSWORD=` boş |
| 12 | shared/config/.env.example | 60-92 | OAuth secret anahtarları, boş değer |
| 13 | shared/config/.env.example | 100-102 | private.pem gitignore + key yolları |
| 14 | api.coremusic.net/.env.example | 5 | "GERÇEK SECRET YAZMA" kuralı |
| 15 | api.coremusic.net/.env.example | 15 | `DB_HOST_PLACEHOLDER` |
| 16 | auth.coremusic.net/config/.env.example | 8-9 | DB kullanıcı/parola boş |
| 17 | shared/src/Log/Redactor.php | 28 | `MASK` değeri |
| 18 | shared/src/Log/Redactor.php | 31 | `SENSITIVE_KEYS` listesi |
| 19 | shared/src/Log/Redactor.php | 40-65 | Redaction kuralları |
| 20 | .ai/architecture/K17-devops/index.md | 19 | Secret-scan sahiplik sınırı (K009) |
| 21 | .ai/architecture/K17-devops/devops-master.md | 26, 30 | Sahiplik: Security + DevOps |

## §12 Log redaction iş birliği (K017 sınırı)

| # | Konu | K018 (bu dosya) | K017 (diğer dosya) | Arayüz |
|---|---|---|---|---|
| 1 | CI logu | Secret basmama kuralı | — | §9 satır 4 |
| 2 | Uygulama logu | — | Redactor maskelemesi | Redactor.php Satır 28 |
| 3 | Hassas anahtar listesi | Şablon anahtarları | `SENSITIVE_KEYS` | Redactor.php Satır 31 |
| 4 | Tarama | GitLeaks (statik) | Redaction (dinamik) | İkisi birlikte |
| 5 | Test | — | Redaction testi mevcut | Paylaşılan test dosyası |

**Kural:** statik tarama (GitLeaks) **depo geçmişini**, dinamik redaction (Redactor) **canlı
logu** korur; biri diğerinin yerine geçmez.

### §12.1 Redaction testi — repo kanıtı

| # | Test / assertion | Dosya satırı | Ne doğruluyor |
|---|---|---|---|
| 1 | `testAuthKeyInQueryStringIsRedacted` | 55 | URI'de `auth_key` yakalanır |
| 2 | `assertStringContainsString('auth_key=[REDACTED]')` | 63 | Maskeleme uygulanır |
| 3 | `assertStringNotContainsString(self::AUTH_KEY)` | 64 | Ham değer logda yok |
| 4 | `"has_auth_key":true` assertion'ı | 66 | Flag değeri korunur (sahte pozitif yok) |
| 5 | Alt-anhtar prefix assertion'ı | 67 | Kısaltılmış önek maskelenmez |
| 6 | `testPasswordIsRedactedInKeyValueAndJsonFormats` | 70 | Parola iki biçimde de maskelenir |

**Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` / Satır: 55 · 61 · 63 · 64 · 66 · 67 · 70**

**Kural:** testteki `self::AUTH_KEY` değeri dosya içinde sabittir ama gerçek secret değildir
(test fixture); üretimde aynı desen gerçek secret ile asla yazılmaz (§9 satır 1).

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Son tarama sonucu bilinmiyor | Kapı kör olabilir | yüksek | CI run kanıtı | ⚠️ VERIFICATION REQUIRED |
| 2 | Özel config yok | Sahte pozitif/negatif | orta | Config kararı | Bilinçli (Satır 4) |
| 3 | Rotation periyodu yok | Eski secret hayatta | orta | Periyot kararı | ⚠️ VERIFICATION REQUIRED |
| 4 | Üretim secret saklama biçimi | Bilinmiyor | orta | Vault kararı | UNKNOWN |
| 5 | Fork PR akışı | Dış sızıntı | orta | Kural doğrulama | ⚠️ VERIFICATION REQUIRED |
| 6 | Action'lar sabit sürümde değil | Sürpriz | düşük | Pin kararı | §6.1 satır 6 |
| 7 | Commit mesajı taraması | Mesaj sızıntısı | düşük | Kapsam kararı | §5.4 satır 3 |
| 8 | JWT anahtar dönüşümü | Eski anahtar riski | düşük | Dönüşüm prosedürü | §7.4 satır 5 |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Kapı | gitleaks job koşuyor | CI hattı | her PR | scan Satır 7-9 |
| 2 | Şablon | Gerçek değer = 0 | Şablon incelemesi | her PR | §7.2 |
| 3 | Yetki | Workflow `permissions` sahibi | ci.yml denetimi | her PR | §6.2 |
| 4 | Gitignore | `.env*` korumalı | .gitignore | aylık | Satır 69-70 |
| 5 | Log | Redaction testi yeşil | Paylaşılan test | her PR | §12 |
| 6 | Envanter | Yeni secret = 3 adım | Kod incelemesi | ekleme anında | §8.1 satır 3 |
| 7 | Sonuç | Tarama raporu okunuyor | CI run kaydı | ⚠️ | §13 risk 1 |

## §15 Sızıntı yanıtı (playbook)

| # | Adım | Eylem | Süre | Sahibi |
|---|---|---|---|---|
| 1 | Tespit | GitLeaks kırmızı ya da insan bildirimi | anında | Kapı / insan |
| 2 | İptal | Secret **derhal iptal** (sağlayıcıda) | ilk 15 dk | Sahip |
| 3 | Yenileme | Yeni secret üretimi (rotation) | ilk saat | Sahip |
| 4 | Temizlik | Git geçmişi temizliği kararı | 24 saat | Onay |
| 5 | Kapı | Yeni değer yalnız ortam değişkeni | 24 saat | §7.2 |
| 6 | Ders | Kural ekleme (config/allowlist) | 1 hafta | §5.2 |
| 7 | Kayıt | Olay kaydı + tarih | 1 hafta | Vault |

### §15.1 Yanıt kuralları

| # | Kural | Zorunlu mu | Gerekçe | Not |
|---|---|---|---|---|
| 1 | İptal yenilemeden önce gelir | evet | Eski hâlâ çalışır | Sıra |
| 2 | Yeni değer loglanmaz | evet | Yinelenen sızıntı | §9 satır 4 |
| 3 | Geçmiş temizliği onaylı | evet | Kırılma riski | §15 adım 4 |
| 4 | Olay kaydı yazılır | evet | Tekrar önleme | §15 adım 7 |
| 5 | Kapı sertleşir | tercih | Tekrar önleme | §5.2 |

### §15.2 Öncelik matrisi

| # | Sınıf | Öncelik | Süre | Not |
|---|---|---|---|---|
| 1 | Üretim DB parolası | kritik | 15 dk | §8 satır 1 |
| 2 | OAuth client secret | yüksek | 1 saat | §8 satır 2 |
| 3 | JWT özel anahtarı | kritik | 15 dk | §8 satır 3 |
| 4 | Figma token | orta | 24 saat | §8 satır 4 |
| 5 | CI token (otomatik) | düşük | otomatik | §8 satır 5 |

### §15.3 Temizlik seçenekleri

| # | Seçenek | Artı | Eksi | Karar |
|---|---|---|---|---|
| 1 | Sadece iptal | Hızlı | Git geçmişi kalır | Asgari |
| 2 | İptal + rotation | Güvenli | Süreç | Zorunlu |
| 3 | + geçmiş rewrite | Temiz | Kırıcı | Onaylı |
| 4 | + tarama genişletme | Önleyici | Gecikme | §5.2 |

### §15.4 Rotation kuralı (taslak)

| # | Alan | Değer | Durum |
|---|---|---|---|
| 1 | Periyot | tanımsız | ⚠️ VERIFICATION REQUIRED |
| 2 | Sahip | secret sınıfı sahibi | §8.1 |
| 3 | Kayıt | Rotation tarihi | §15.1 satır 4 |
| 4 | Otomasyon | yok | ⚠️ VERIFICATION REQUIRED |

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| GitHub, Actions'ta secret kullanımını (Using secrets in GitHub Actions) tanımlar | https://docs.github.com/en/actions/security-for-github-actions/security-guides/using-secrets-in-github-actions | 2026-10-12 | HTTP 200 doğrulandı |
| GitHub, secret scanning hizmetini (Secret scanning) tanımlar | https://docs.github.com/en/code-security/secret-scanning/introduction/about-secret-scanning | 2026-10-12 | HTTP 200 doğrulandı |
| GitHub, Actions güvenlik sertleştirmesini (Secure use reference) tanımlar | https://docs.github.com/en/actions/security-for-github-actions/security-guides/security-hardening-for-github-actions | 2026-10-12 | HTTP 200 doğrulandı |
| Docker, build sırasında secret kullanımını (Build secrets) tanımlar | https://docs.docker.com/build/building/secrets/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 10 yeni
derin-branch dosyasında bulunmaz.

## §17 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık kaydedildi | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | secret-scan.yml | tam okuma (31 satır) | Satır 4, 26, 28-31 doğrulandı | 2026-10-12 |
| 5 | `.env.example` glob | 3 dosya | 0 gerçek `.env` | 2026-10-12 |
| 6 | Şablon değerleri | dosya okuma | Boş/placeholder (doğrulandı) | 2026-10-12 |
| 7 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 8 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 9 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 10 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 11 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
