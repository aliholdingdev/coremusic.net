---
node_id: K013-integraion/external-systems/external-dogrulama
node_type: architecture-validation
title: "Dış Sistemler — Doğrulama ve Test Kapıları"
type: validation
status: active
date: 2026-10-16
parent_id: K013-integraion/external-systems
children: []
depends_on:
  - K013-integraion/external-systems/external-anayasa
  - K013-integraion/external-systems/external-sistem-envanteri
evidence:
  - "https://developers.soundcloud.com/docs"
  - "https://musicbrainz.org/doc/MusicBrainz_API"
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"
validation:
  - "Katman tanımları envanter satırlarındaki kimlik tipleriyle eşleşiyor (L1–L4)"
  - "CI kapısı exit-code sözleşmesi açıkça tanımlı (§6)"
open_questions:
  - "Canlı (üretim-üstü) kimlik akışı testleri için hangi ortam anahtarları kullanılacak → PLACEHOLDER/secret store, üst otorite"
group: kod
---

# Dış Sistemler — Doğrulama ve Test Kapıları

> **Tarih:** 2026-10-16 · **Parent:** `K013-integraion/external-systems` ·
> **Mode:** Red Team · Human Mode · Truth Mode
> Bu dosya: envanterdeki her dış sistem satırının **nasıl doğrulandığını** ve
> **ne zaman yeniden doğrulanacağını** tanımlar. Kod içeriği yer tutucu (PLACEHOLDER)
> düzeyindedir; gerçek anahtar/secret kullanılmaz.

## 1. Doğrulama Katmanları

| Katman | Ne doğrular | Ne zaman | Çıktı |
|--------|-------------|----------|-------|
| **L1 — Kaynak erişilebilirliği** | Kanıt URL hâlâ canlı mı (200, içerik var) | Her oturum/dönemsel | URL durumu + tarih |
| **L2 — Şema bütünlüğü** | Envanter satırı 10 alanı tam taşıyor mu | Dosya yazımında | alan eksik var/yok |
| **L3 — Kimlik akışı sözleşmesi** | Akış adımları kaynakla tutarlı mı (imza, challenge, token ucu) | Kimlik kodu değişince | adım listesi vs kaynak |
| **L4 — Canlı uç testi** (opsiyonel) | Base URL + auth bir uçta gerçekten cevap veriyor mu | Kapsam kararı sonrası | HTTP durum kodu |

**Kural:** L1–L3 **zorunlu** (kanıt dosyası olmadan satır geçersiz). L4 yalnızca
kapsam `aktif` olan sistemler için çalıştırılır ve **üretim secret'ı kullanmaz**
(ayrı ortam/PLACEHOLDER).

## 2. L1 — Kaynak Erişilebilirlik Testi

**Amaç:** `evidence[]` içindeki her URL'in erişilebilir olduğunu göstermek.

```bash
# L1 — tek URL kontrolü (bash/CI örneği; yer tutucu değiller, genel URL'lerdir)
check_url() {
  code=$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 20 -A "CoreMusic-Vault-Check/1.0" "$1")
  if [ "$code" = "200" ]; then echo "OK   $1"; return 0; fi
  echo "FAIL($code) $1"; return 1
}
```

| Sistem | Kanıt URL (L1 adayı) | Beklenen |
|---|---|---|
| Spotify | developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | 200 |
| Apple Music | developer.apple.com/documentation/applemusicapi/generating-developer-tokens | 200 (gerekirse exa `.md` yolu) |
| YouTube | developers.google.com/youtube/v3/docs → redirect | 200 |
| SoundCloud | developers.soundcloud.com/docs | 200 |
| MusicBrainz | musicbrainz.org/doc/MusicBrainz_API | 200 |
| Discogs | www.discogs.com/developers | 403 olabilir → exa `.md` fallback (bkz §7) |

**Son 1.000 ms kuralı:** L1 çalışırken yanıt süresi ölçülür; >3000 ms olan URL
`SLOW` etiketi alır (erişilebilirlik = OK, performans ayrı not).

## 3. L2 — Şema Bütünlüğü Testi

**Amaç:** `external-sistem-envanteri.md` satırları anayasa §3'teki 10 alanı taşıyor mu.

```text
KONTROL L2 (her envanter satırı için):
  [ ] sistem adı        [ ] sağlayıcı      [ ] kimlik tipi
  [ ] API sürümü        [ ] durum          [ ] uç nokta (base)
  [ ] risk notu         [ ] kanıt URL      [ ] doğrulama tarihi
  [ ] not (boş bile olsa "-")
SUÇ: eksik alan → satır "GEÇERSİZ", envanter dosyası commit edilmez
SUÇ: kanıt URL'siz satır → anayasa P2 ihlali
SUÇ: "durum: aktif" ama üst otorite onayı yok → anayasa §6 ihlali
```

**Exit-code:** 0 = tüm satırlar geçerli · 1 = en az bir satır geçersiz ·
2 = dosya/şema okunamadı.

## 4. L3 — Kimlik Akışı Sözleşme Testi

**Amaç:** Adaptör kodu yazılırken akış adımlarının kaynakla (§2 web-arastirma)
birebir örtüşmesini zorlamak.

| Sistem | Sözleşme maddeleri (kaynak ile birebir) | Test tipi |
|---|---|---|
| Spotify | `code_challenge_method=S256` · `code_verifier` 43–128 · `POST accounts.spotify.com/api/token` · `grant_type=authorization_code` | sözleşmeli unit test (imza formatı) |
| Apple Music | header `alg=ES256` + `kid` (10 karakter) · payload `iss/iat/exp/origin` · `exp ≤ 15777000` | JWT yapı testi (imzalama canlı değil) |
| SoundCloud | `secure.soundcloud.com/authorize` + S256 · `grant_type=authorization_code` + `code_verifier` · refresh **tek kullanımlık** · header `Authorization: OAuth` | akış sözleşmesi + yenileme davranışı |
| YouTube | base `www.googleapis.com/youtube/v3` · resource adı `playlist`/`video` ... (**`music` YOK**) | negatif test: `music` resource'una istek beklenen 404 |
| Last.fm | parametre alfabetik sıralama + `api_secret` + MD5 → `api_sig` (32 karakter) | imza üretimi unit test |
| MusicBrainz | `Accept: application/json` veya `fmt=json` · **1 çağrı/sn** throttle · User-Agent dolu | throttle + header testi |
| Discogs | User-Agent zorunlu · auth'sız 25/dk sınırı | header + rate-limit (mock) |
| Deezer | app id + redirect uri eşleşmesi | redirect uri eşitlik testi |

**Örnek (yer tutucu — Apple Music JWT yapı kontrolü):**

```python
# Dil: Python — Rol: JWT yapı doğrulama (YER TUTUCU; anahtar DEĞERİ asla yazılmaz)
import base64, json

def b64url(data: bytes) -> str:
    return base64.urlsafe_b64encode(data).rstrip(b"=").decode()

def check_dev_token_claims(claims: dict) -> list[str]:
    """Apple Music geliştirici token'ı yapı kontrolü — kaynak: generating-developer-tokens"""
    errors = []
    for field in ("iss", "iat", "exp", "origin"):
        if field not in claims:
            errors.append(f"eksik claim: {field}")
    if claims.get("exp") is not None and claims["exp"] > 15_777_000:
        errors.append("exp 15777000 sınırını aşıyor")
    return errors  # boş liste = yapı geçerli

# kid: header'da 10 karakterlik key ID — değeri secret store'dan gelir
# KID_PLACEHOLDER = "<KEY_ID_10_CHARS>"   # asla gerçek değer yazılmaz
```

**Örnek (yer tutucu — MusicBrainz throttle):**

```python
# Dil: Python — Rol: 1 çağrı/sn sınırı (kaynak: MusicBrainz_API rate limiting)
import time

class OnePerSecondGate:
    MIN_INTERVAL = 1.0  # saniye — kaynak: "never make more than ONE call per second"

    def __init__(self) -> None:
        self._last = 0.0

    def wait(self) -> None:
        delta = time.monotonic() - self._last
        if delta < self.MIN_INTERVAL:
            time.sleep(self.MIN_INTERVAL - delta)
        self._last = time.monotonic()
```

## 5. L4 — Canlı Uç Testi (kapsam sonrası)

**Ön koşul:** Envanter satırı `aktif` + üst otorite onayı + ayrı ortam anahtarları
(`PLACEHOLDER` → secret store).

```bash
# L4 — kapsamı aktif sistemler için (PLACEHOLDER değerlerle; gerçek secret YOK)
# 1) public uç: auth'sız beklenti
curl -s -o /dev/null -w '%{http_code}\n' \
  -A "CoreMusic/1.0 (contact: PLACEHOLDER_EMAIL)" \
  "https://musicbrainz.org/ws/2/artist?query=beatles&fmt=json"
# Beklenen: 200 (auth'sız okuma)

# 2) auth'lı uç: token header doğrulaması (değerler secret store'dan)
# Beklenen: geçersiz token → 401; geçerli token → 200/403 (izin) — 5xx = ALTYAPI HATASI
```

**Beklenti matrisi:**

| Durum | HTTP | Karar |
|---|---|---|
| Geçersiz/imzasız token | 401 | ✅ Beklenen (Red Team: doğru reddediyor) |
| İzin yok | 403 | ✅ Beklenen (kapsam dışı kaynak) |
| Bulunamadı | 404 | ✅ Beklenen (var olmayan resource) |
| Rate limit | 429 | ⚠️ Beklenen ama **backoff zorunlu** |
| Sunucu hatası | 5xx | ❌ Beklenmeyen → olay kaydı |

## 6. CI Kapısı ve Exit-Code Sözleşmesi

| Kapı | Koşul | Exit-code | Bloklar |
|---|---|---|---|
| `GATE-EXT-1` L1 | evidence URL'ler erişilebilir | 0/1 | bayat kanıt raporu üretir |
| `GATE-EXT-2` L2 | envanter satırları 10 alanlı | 0/1/2 | geçersiz satır varsa dosyayı |
| `GATE-EXT-3` L3 | kimlik sözleşmesi testleri yeşil | 0/1 | adaptör kodu merge'ünü |
| `GATE-EXT-4` secret taraması | `.md` içinde gerçek anahtar/`BEGIN PRIVATE KEY`/uzun rastgele dizesi yok | 0/1 | her commit'i (**P5**) |
| `GATE-EXT-5` L4 (ops.) | canlı uç beklentileri | 0/1 | sürüm çıkışını (yalnız `aktif`) |

**Secret taraması (GATE-EXT-4) örnek desenleri:**

```text
YASAK (dosyada geçerse exit 1):
  -----BEGIN (RSA | EC | OPENSSH)? PRIVATE KEY-----
  client_secret\s*[:=]\s*["'][A-Za-z0-9]{20,}["']
  api_key\s*[:=]\s*["'][A-Fa-f0-9]{32,}["']
İzinli: PLACEHOLDER, <...>, env(PLACEHOLDER_VAR), \$ENV{...}
```

## 7. Dönemsel Denetim Takvimi

| Sıklık | Görev | Katman | Not |
|---|---|---|---|
| Her oturum (K013 işi) | Kanıt tarihi taze mi? (>90 gün → yeniden çek) | L1 | bayat kanıt = `⚠️ VERIFICATION REQUIRED` |
| Haftalık | Envantere yeni sistem/durum değişimi diff'i | L2 | üst otorite onayı ile |
| Aylık | Aktif sistemlerde canlı uç duman testi | L4 | 401/403 matrisi §5 |
| Sürüm öncesi | GATE-EXT-1..4 tam yeşil | hepsi | exit-code §6 |

**Fetch fallback zinciri (L1):**

1. Doğrudan `webfetch` (text) → boş/403/403 ise
2. `exa web_fetch_exa` (`.md` son ekli yol) → hâlâ boş ise
3. URL **elenir** + nedeni `external-web-arastirma.md` §5'e yazılır (sessiz kayıp YOK).

## 8. Bilinen Test Kısıtları (Red Team dürüstlük listesi)

| # | Kısıt | Etki |
|---|---|---|
| 1 | Spotify/Deezer canlı kayıt akışı **canlı test edilmedi** | ⚠️ VERIFICATION REQUIRED (anayasa §8.3) |
| 2 | Apple Music base URL `api.music.apple.com` dokümantasyon referansıdır; canlı 200 test edilmedi | L4 kapısında kapanır |
| 3 | Deezer API sayfası login-gated → uç nokta listesi çekilemedi | envanter §5'te not edildi |
| 4 | YouTube "resmi API yok" iddiası negatif kanıttır; ToS "e.g." istisnası var | kesinlik notu + üst karar |
| 5 | L4 canlı testlerde gerçek secret **kullanılmaz** (P5) → tam yetki doğrulaması yapılamaz | yetki testi ayrı güvenlik işi |

## 9. Doğrulama Kaydı Şablonu

```markdown
## Doğrulama Kaydı — <sistem> — <YYYY-MM-DD>
- L1 URL: <url> → <200/403/404> (yöntem: direct/exa)
- L2 şema: 10/10 alan → GEÇERLİ
- L3 sözleşme: <madde listesi> → GEÇTİ
- L4 canlı: <var/yok> → <HTTP beklentisi/gerçekleşen>
- Secret taraması (GATE-EXT-4): temiz
- Karar: <durum önerisi — üst otoriteye sunulur>
```

## 10. Test Senaryo Kataloğu (L3/L4 için)

| ID | Sistem | Senaryo | Düzen | Beklenen |
|---|---|---|---|---|
| T-SP-01 | Spotify | `code_verifier` uzunluğu 43–128 dışında | negatif | üretici hata fırlatır |
| T-SP-02 | Spotify | `state` doğrulamasız callback | negatif | reddet (CSRF) |
| T-SP-03 | Spotify | token ucu yanlış `grant_type` | negatif | 400 |
| T-AP-01 | Apple | `exp > 15777000` JWT | negatif | 401 |
| T-AP-02 | Apple | `kid` 10 karakter değil | negatif | 401 |
| T-AP-03 | Apple | header `alg` ES256 değil | negatif | 401 |
| T-SC-01 | SoundCloud | refresh token ikinci kez kullanım | negatif | 401 (tek kullanımlık) |
| T-SC-02 | SoundCloud | `code_challenge_method` yok | negatif | authorize reddi |
| T-SC-03 | SoundCloud | client credentials 51. token | negatif | rate limit hatası |
| T-YT-01 | YouTube | `/music` resource isteği | negatif | 404 (Music resource YOK) |
| T-YT-02 | YouTube | API key'siz istek | negatif | 400/401 |
| T-LF-01 | Last.fm | `api_sig` yanlış sıralama | negatif | imza hatası |
| T-LF-02 | Last.fm | `api_sig` 32 karakter değil | negatif | imza hatası |
| T-MB-01 | MusicBrainz | 2 istek/sn | negatif | throttle 2. isteği bekletir |
| T-MB-02 | MusicBrainz | boş User-Agent | negatif | engel/uyarı (kaynak: zorunlu) |
| T-DC-01 | Discogs | User-Agent'sız istek | negatif | 403 |
| T-DC-02 | Discogs | 26. istek/dk (auth'sız) | negatif | 429 |
| T-DZ-01 | Deezer | farklı `redirect_uri` | negatif | redirect hatası |
| T-ALL-01 | Tümü | `.md` içinde gerçek secret sızıntısı | güvenlik | GATE-EXT-4 exit 1 |

## 11. Hata Sınıflandırma ve Müdahale

| Sınıf | Örnek | Müdahale | Kayıt |
|---|---|---|---|
| Beklenen kimlik reddi | 401 | token yenile / üretimi tazele | sessiz (log debug) |
| İzin reddi | 403 | kapsam dışı → envanter notu | log info |
| Yok | 404 | resource adı / id kontrolü | log info |
| Rate limit | 429 | **backoff (exponential, jitter)**; throttle kapıları §4/§10 | log warn |
| ToS/erişim kısıtı | 403 tekrarlayan / hesap kısıtı | **dur** → üst otorite eskalasyonu (R-Yüksek) | log error + olay |
| Altyapı | 5xx | retry (max 3) + olay kaydı | log error |
| Kanıt bayatlığı | L1 fail | URL yeniden çek → web-arastirma §5'e elme nedeni | rapor |
| Şema ihlali | L2 fail | satır geçersiz → commit engeli | GATE-EXT-2 |

## 12. CI Workflow Şeması (mantık — gerçek pipeline devops agent'ında)

```yaml
# Mantık şema — GATE-EXT kapılarının sırası (workflow yaml'ını devops agent'ı yazar)
name: external-systems-gates
on: [push, pull_request]
jobs:
  ext-gates:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: GATE-EXT-4 secret-taramasi   # en önce: sızıntı varsa anlamlı değil
        run: python tools/secret_scan.py .ai/architecture/K013-integraion/external-systems/
      - name: GATE-EXT-2 sema
        run: python tools/inventory_l2_check.py  # exit 0/1/2 (§3)
      - name: GATE-EXT-3 sozlesme testleri
        run: pytest tests/contracts/ -q            # §4 maddeleri
      - name: GATE-EXT-1 kanit L1 (dönemsel/manuel tetik)
        if: github.event_name == 'schedule'
        run: python tools/evidence_l1_check.py --max-age-days 90
  # GATE-EXT-5 (L4) yalnız release dalında ve 'aktif' envanter satırlarıyla
```

**Sıra gerekçesi:** Secret taraması en başta (§6); kanıt L1'i her PR'da değil
dönemsel çalışır (§7) ki PR süresi şişmesin — kapı sözleşmesi değişmez.

## 13. Doğrulama Dosyası Kapanış Kontrol Listesi

- [x] L1–L4 katmanları tanımlı (§1)
- [x] L1 URL matrisi envanter kanıtlarıyla eşleşiyor (§2 ↔ envanter §11)
- [x] L2 10 alan kontrolü anayasa §10 kurallarıyla birebir (§3)
- [x] L3 sözleşme maddeleri web-arastirma §2 kaynaklarıyla birebir (§4)
- [x] L4 beklenti matrisi (401/403/404/429/5xx) (§5)
- [x] CI exit-code sözleşmesi (§6) + workflow mantığı (§12)
- [x] Dönemsel denetim + fetch fallback zinciri (§7)
- [x] Bilinen kısıtlar dürüstçe listelendi (§8)
- [x] Test senaryo kataloğu (§10) + müdahale sınıfları (§11)
- [ ] **Canlı L4** → yalnız `aktif` + üst onay sonrası (açık madde)

---

## 14. Doğrulama Sözlüğü

| Terim | Tanım |
|---|---|
| L1 | Kanıt URL erişilebilirlik kontrolü (§2) |
| L2 | Envanter 10 alan şema bütünlüğü (§3) |
| L3 | Kimlik akışı sözleşmesi testi (§4) |
| L4 | Canlı uç testi — yalnız `aktif` (§5) |
| GATE-EXT-n | CI çıkış kodu kapısı (§6) |
| Bayat kanıt | Doğrulama tarihi >90 gün (§7) |
| Fetch fallback | direct → exa `.md` → elme zinciri (§7) |
| Beklenti matrisi | HTTP durumu → beklenen karar tablosu (§5) |
| Duman testi | Minimal canlı uç çağrısı (§5, §7) |
| Sözleşmeli test | Kod davranışı ↔ kaynak cümlesi eşleşmesi (§4) |

## 15. Kapı Özeti (exit-code tekrar kartı)

| Kapı | Soru (tek cümle) | Fail → sonucu |
|---|---|---|
| GATE-EXT-1 | Kanıt URL'leri hâlâ yaşıyor mu? | bayat kanıt raporu (L1) |
| GATE-EXT-2 | Envanter satırları 10 alanlı mı? | satır geçersiz, commit engeli |
| GATE-EXT-3 | Kimlik akışı kaynakla tutarlı mı? | adaptör merge'i engeli |
| GATE-EXT-4 | `.md` içinde gerçek secret var mı? | her commit engeli (en öncelikli) |
| GATE-EXT-5 | `aktif` sistemler canlıda beklentiye uyuyor mu? | sürüm çıkışı engeli (opsiyonel) |

**Sıralama gerekçesi:** GATE-EXT-4 her zaman en önce çalışır — secret sızıntısı
varken diğer kapıların sonucunun anlamı yoktur (güvenlik > süreklilik).

## §GRUPLAR

| Grup | Bu dosyadaki karşılığı |
|---|---|
| mimari | Katman tanımı (§1), karar mercii (L4 ön koşulu §5), denetim takvimi (§7) |
| kod | L3/L4 örnek bloklar (Python/bash, PLACEHOLDER), CI gate desenleri (§6) |
| kanit | L1 URL matrisi (§2), bilinen kısıtlar (§8), doğrulama kaydı şablonu (§9) |

## §Kaynak

| Kaynak | Tarih | Erişim |
|---|---|---|
| `external-web-arastirma.md` §2–§9 (14 kanıt, günlük, alıntılar) | 2026-10-16 | disk |
| `external-sistem-envanteri.md` (kimlik matrisi §9, uç nokta §11) | 2026-10-16 | disk |
| `external-anayasa.md` §3/§10 (şema + geçerlilik) | 2026-10-16 | disk |
| `api-contracts/openapi/openapi-dogrulama.md` (kalıp kapı yapısı) | kalıp | disk |
| MusicBrainz rate limiting · SoundCloud API Guide · Spotify PKCE tutorial · Last.fm authspec · Discogs developers | 2026-10-16 | webfetch (bkz web-arastirma §7) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
