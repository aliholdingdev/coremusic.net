---
node_id: K009-security/security-testing/security-testing-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-bilesenler
  - K009-security/security-testing/security-testing-dogrulama
evidence:
  - "6 URL — HTTP 200 doğrulandı 2026-10-15 (§1 tablosu)"
  - C:\www\coremusic.net\shared\composer.json·46-49
  - C:\www\coremusic.net\.github\workflows\ci.yml·141-186
validation: Faz 7 ön draft — URL erişim kontrolü 2026-10-15; sayfa içeriği iddiaları tazedir, sürüm iddiası YOK
open_questions:
  - "Infection corpus/mutation koşusu repo'da yok ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K009 · security-testing · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kaynak Doğrulaması

Bu modül, güvenlik testi disiplinine **dış kaynak desteği** veren 6 URL'yi
kayıt altına alır. Her satır 2026-10-15'te **HTTP 200** ile doğrulanmıştır.
URL havuzu dar tutulmuştur: `security-testing/index.md` ve K009 kök
(`security-web-arastirma.md`) dosyalarındaki zaproxy / WSTG / ASVS bağlantıları
**tekrarlanmaz** — buradaki 6 kaynak statik analiz, mutation testing ve test
çalıştırıcı ailedir.

**TT-12 · Web araştırması KK:** Her satır `İddia | URL | Tarih: 2026-10-15 | HTTP 200`
şemasındadır; şema dışı satır sayılmaz. Yeni kaynak eklenirken önce index ve K009
kök havuzu kontrol edilir (tekrar yasak).

---

## §1. Kaynak Tablosu (6 URL · HTTP 200 · 2026-10-15)

| İddia | URL | Tarih: 2026-10-15 | HTTP 200 |
|---|---|---|---|
| OWASP, kaynak kod analizi araçlarını (SAST kategorisi) toplayan topluluk sayfası yayımlar — güvenlik testi envanterinin SAST ayağı bu sınıfa girer | https://owasp.org/www-community/Source_Code_Analysis_Tools | Tarih: 2026-10-15 | HTTP 200 |
| PHPStan'ın resmi sitesi statik analiz (type/error) aracı sunar; repo'daki `composer stan` script'inin kaynağıdır | https://phpstan.org/ | Tarih: 2026-10-15 | HTTP 200 |
| phpstan/phpstan GitHub deposu aracın açık kaynak yayın kanalıdır; sürüm ve release kayıtları buradadır | https://github.com/phpstan/phpstan | Tarih: 2026-10-15 | HTTP 200 |
| Infection, PHP için mutation testing aracıdır — testlerin hatayı gerçekten yakalayıp yakalamadığını ölçer | https://infection.github.io/ | Tarih: 2026-10-15 | HTTP 200 |
| Semgrep, hafif statik analiz/kalıp taraması aracıdır; CI'da kod tarama katmanı olarak konumlanır | https://semgrep.dev/ | Tarih: 2026-10-15 | HTTP 200 |
| sebastianbergmann/phpunit deposu, repo'nun test çalıştırıcısının (phpunit ^10.5) upstream kaynağıdır | https://github.com/sebastianbergmann/phpunit | Tarih: 2026-10-15 | HTTP 200 |

**Sayım:** 6 satır, 6 benzersiz URL, 0 tekrar (index + K009 kök havuzu hariç tutuldu).

---

## §2. Kod Çapraz Referans (§2 kod xref)

Her kaynak, repo'daki somut karşılığına bağlanır:

| # | Kaynak (§1) | Repo karşılığı | Dosya·Satır |
|---|---|---|---|
| X-1 | OWASP Source Code Analysis Tools | CI kod tarama job'ları (SAST ailesi) | `.github/workflows/ci.yml·141-186` |
| X-2 | phpstan.org | `scripts.stan` + require-dev `phpstan/phpstan ^1.10` | `shared/composer.json·23-26,47` |
| X-3 | github.com/phpstan/phpstan | require-dev pin'i (surüm kaynağı) | `shared/composer.json·47` |
| X-4 | infection.github.io | **karşılığı YOK** — mutation koşusu repo'da yok | ⚠️ (bkz. §4 B-15) |
| X-5 | semgrep.dev | kod tarama katmanının harici alternatifi (ci.yml'de Codacy var) | `.github/workflows/ci.yml·141-186` |
| X-6 | github.com/sebastianbergmann/phpunit | require-dev `phpunit/phpunit ^10.5` + bootstrap | `shared/composer.json·46-49` · `shared/phpunit.xml·4` |

---

## §3. Kaynakların Kullanım Yeri (hangi modül beslenir)

| Kaynak | Beslediği modül | Kullanım |
|---|---|---|
| OWASP SAST araç listesi | `security-testing-anayasa.md` §3 (araç sınıflandırma) | SAST kategorisinin tanımı |
| phpstan.org + phpstan repo | `security-testing-bilesenler.md` §3, `security-testing-surucu.md` G-06 | Statik analiz bileşeni + gate |
| Infection | `security-testing-anayasa.md` §4 (derinlik ölçütü) | Mutation testing — test kalitesi ölçütü (henüz uygulanmadı) |
| Semgrep | `security-testing-surucu.md` §2 (tarama katmanı) | CI kod tarama katmanının referansı |
| phpunit repo | `security-testing-bilesenler.md` §2-§3 | Test çalıştırıcı upstream'i |

Kural: kaynak **tekrarlanmaz**, bağlanır — içeriği burada özetlenir, modüllerde
yalnız `Dosya·Satır` ile kullanılır (TT-12).

---

## §4. Araştırma Boşlukları (⚠️)

| # | Boşluk | Durum |
|---|---|---|
| B-15 | Infection mutation koşusu repo'da yok (require-dev'te de değil) — X-4 karşılıksız | ⚠️ VERIFICATION REQUIRED |
| B-16 | Sayfa içeriği 2026-10-15'te okundu; içeriğin sürüm/bölüm detayları iddiaya alınmadı (yalnız erişim + genel içerik iddiası) | ⚠️ — sürüm iddiası YOK, zero-hallucination korunur |
| B-17 | Semgrep repo'da kurulu değil (Codacy job'ları var) — X-5 "alternatif" konumunda | ⚠️ VERIFICATION REQUIRED |
| B-18 | OWASP listesinin tamamı taranmadı; yalnız kategori varlığı iddia edildi | ⚠️ — derin okuma yapılmadı |

---

## §5. Havuz Tekrar Kontülü

| Havuz | URL örneği | Bu dosyada var mı? |
|---|---|---|
| `security-testing/index.md` URL havuzu | (index §1'deki kaynaklar) | ❌ yok |
| `K009-security/security-web-arastirma.md` kök havuzu | zaproxy, WSTG, ASVS (bkz. kök dosya) | ❌ yok |
| Bu dosya (§1) | OWASP SAST tools, phpstan.org ×2, Infection, Semgrep, phpunit | ✅ 6 benzersiz |

**Sonuç:** 0 tekrar (TT-12 uyumlu).

---

## §6. Doğrulama Notu (status: verify-required nedeni)

- **Yapılan:** 6 URL 2026-10-15'te HTTP 200 ile erişildi (§1 `Tarih`/`HTTP 200`
  sütunları) + §2 kod xref `Dosya·Satır` ile teyit edildi.
- **Yapılmayan:** sayfa içeriğinin derin/sürümlü okuması (B-16), Infection/Semgrep
  kurulum kararı (B-15, B-17).
- **Geçiş kuralı:** `verify-required` → `passed` yalnız §4 boşluklarının kararı
  (kapat veya bilinçli kapsam dışı) ve iddiaların yeniden erişim tazelemesiyle.

---

## §7. Kaynak Kullanım Kuralları

1. **Tekrar yasak:** aynı URL index/K009 kök havuzunda varsa buraya eklenmez.
2. **İddia dar tutulur:** sayfadan yalnız erişim + genel içerik iddiası alınır; sürüm numarası okunmadan yazılmaz (B-16).
3. **Kod bağı her zaman:** her kaynak §2'de en az bir `Dosya·Satır` ile bağlanır.
4. **Karşılıksız kaynak açık kalır:** repo'da eşleşmeyen araç (Infection, Semgrep) §4'te `⚠️` durur.
5. **Yenileme:** URL'ler her revizyonda yeniden erişilir; `HTTP 200` tazelenmezse satır düşer.

---

## §BAĞLANTILAR

- [[K009-security/security-testing/index]] — giriş, §3 dosya haritası (web-arastirma satırı)
- [[K009-security/security-testing/security-testing-anayasa]] — §3 araç sınıflandırma (X-1, X-4 hedefi)
- [[K009-security/security-testing/security-testing-surucu]] — G-06/gözetim kapıları (X-2, X-5 hedefi)
- [[K009-security/security-testing/security-testing-bilesenler]] — bileşen envanteri (X-2, X-6 karşılığı)
- [[K009-security/security-testing/security-testing-dogrulama]] — AD-xx kanıt satırları (pipeline'ın kanıt ayağı)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
