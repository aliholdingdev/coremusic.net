---
node_id: K013-integraion/external-systems/external-anayasa
node_type: architecture-constitution
title: "Dış Sistemler — Anayasa ve Kayıt Şeması"
type: architecture
status: active
date: 2026-10-16
parent_id: K013-integraion/external-systems
children:
  - K013-integraion/external-systems/external-sistem-envanteri
  - K013-integraion/external-systems/external-dogrulama
  - K013-integraion/external-systems/external-kod-ornekleri
  - K013-integraion/external-systems/external-web-arastirma
  - K013-integraion/external-systems/index
depends_on:
  - K013-integraion/integration-master
  - K013-integraion/api-contracts/openapi/openapi-anayasa
evidence:
  - "https://github.com/mbianchidev/open-playlist-engine"
  - "https://www.rfc-editor.org/rfc/rfc7636"
validation:
  - "Kayıt şeması alanları external-sistem-envanteri.md satırlarında birebir kullanılıyor"
  - "Kapsam sınırı: envanter bu klasörde, adaptör kodu child seviyede (bkz §4)"
open_questions:
  - "Hangi dış sistemlerin 1. dalga kapsamında olduğu (aktif/beklemede) üst otorite kararı"
group: mimari
---

# Dış Sistemler — Anayasa ve Kayıt Şeması

> **Tarih:** 2026-10-16 · **Parent:** `K013-integraion/external-systems` ·
> **Mode:** Red Team · Human Mode · Truth Mode
> Bu dosya bu klasörün anayasasıdır: dış sistemlerin **nasıl kaydedildiğini**,
> **kimin neye karar verdiğini** ve **kapsamın nerede bittiğini** tanımlar.

## 1. ELI15 (Herkese Açık Dil)

CoreMusic, müzikle ilgili **dış sistemlerle** (Spotify, Apple Music, SoundCloud,
MusicBrainz vb.) konuşmak istiyor. Bu sistemlerin her biri farklı anahtar,
farklı kapı (API) ve farklı kural istiyor. Bu klasör bir **künye defteri** gibidir:

- Buraya **kayıt** yazarız: hangi sistem, hangi sağlayıcı, hangi kimlik tipi,
  hangi API sürümü, durumu ne, riski ne, kanıtı hangi URL.
- Buraya **kod** yazmayız — kod ayrı yerde (child dosyalar / `adapters/`).
- Burada **karar** vermeyiz — "şu sistemi kullanalım mı?" üst otoritenin işidir;
  biz kanıtı hazırlarız (`external-web-arastirma.md`).
- Bilinmeyen bir şeye asla "galiba şöyle" demeyiz: `⚠️ VERIFICATION REQUIRED`
  yazarız.

**Tek cümle:** Bu klasör = dış sistem envanteri (ne var, ne durumda, kanıtı ne)
+ doğrulama kuralları (nasıl test ederiz) + kod örnekleri (nasıl görünür),
**kapsam kararı ve adaptör implementasyonu hariç**.

## 2. İlkeler (Değiştirilemez)

| # | İlke | Açıklama |
|---|------|----------|
| P1 | Zero-Hallucination | Var olmayan sistem/sürüm/uc nokta yazılmaz; doğrulanamayan `⚠️ VERIFICATION REQUIRED` |
| P2 | Kanıt zorunlu | Her dış sistem satırı en az 1 resmi URL (evidence) taşır |
| P3 | Envanter ≠ Adaptör | Bu klasör envanteri tutar; adaptör kodu child seviyede (`adapters/`) |
| P4 | Karar üst otoritede | Kapsam/durum kararları (aktif-beklemede-kapsam-dışı) vault sahibine aittir; subagent kanıt sunar |
| P5 | Secret yok | `.md` içine gerçek anahtar/token/yazıcı anahtarı YAZILMAZ; yalnız `PLACEHOLDER` |
| P6 | Kapsam kapısı | "Bu sistem entegre edilecek mi?" sorusu envanter satırı eklemez, üst karar ister |
| P7 | Tarih damgası | Her kanıt dosyası tarihli; bayat kanıt yeniden çekilir |
| P8 | Dokunulmaz | Klasör adı `integraion` (IMLA) değiştirilmez; `external-` öneki korunur |

## 3. Kayıt Şeması (Envanter Satırı Alanları)

Her dış sistem kaydı aşağıdaki alanları **tam** taşır (eksik alanlı kayıt üretilmez):

| Alan | Zorunlu | Açıklama | Örnek |
|------|---------|----------|-------|
| sistem adı | ✅ | İnsan-okur ad | Spotify |
| sağlayıcı | ✅ | Hukuki/teknik sağlayıcı | Spotify AB (developer.spotify.com) |
| kimlik tipi | ✅ | OAuth2 PKCE / API key / JWT-ES256 / imza / yok | OAuth 2.1 + PKCE (S256) |
| API sürümü | ✅ | Sürüm/revizyon bilgisi | Web API (v1 ailesi) |
| durum | ✅ | `aktif` / `beklemede` / `kapsam-dışı` / `bilinmiyor` | beklemede |
| uç nokta (base) | ✅ | Taban URL | `https://api.spotify.com` |
| risk notu | ✅ | Rate limit / ToS / erişim kısıtı vb. | rate limit hesap bazlı |
| kanıt URL | ✅ | En az 1 resmi kaynak | developer.spotify.com/... |
| doğrulama tarihi | ✅ | Kanıtın çekildiği tarih | 2026-10-16 |
| not | opsiyonel | Sınır durumlar, kesinlik notları | ToS kesinlik notu |

**Durum sözlüğü:**

| Durum | Anlamı |
|-------|--------|
| `aktif` | Üst otorite onaylı, entegrasyon planında |
| `beklemede` | Kanıt hazır, kapsam kararı verilmedi |
| `kapsam-dışı` | Karar: entegre edilmeyecek (gerekçe kayıtlı) |
| `bilinmiyor` | Erişim/sürüm doğrulanamadı → `⚠️ VERIFICATION REQUIRED` |

## 4. Kapsam Sınırı (bu klasörün bittiği yer)

```
K013-integraion/
├── integration-master.md        → ana mimari + §4 iddialar + §7 açık sorular
├── api-contracts/openapi/       → API sözleşmeleri (openapi kalıbı)
└── external-systems/            → BURASI: dış sistem envanteri
    ├── index.md                 → indeks (children, evidence, kapılar)
    ├── external-anayasa.md      → bu dosya (kural)
    ├── external-sistem-envanteri.md → kayıtlar (şema §3)
    ├── external-web-arastirma.md    → kanıtlar (URL doğrulama)
    ├── external-dogrulama.md    → nasıl test ederiz (katmanlar, CI kapısı)
    └── external-kod-ornekleri.md → dil başına minimal örnek (PLACEHOLDER)
```

| Bu dosyada VAR | Bu dosyada YOK (nerede) |
|---|---|
| Kayıt şeması, durum sözlüğü, ilkeler | Adaptör/implementasyon kodu (child `adapters/`) |
| Kapsam kapısı tanımı | Kapsam kararı (üst otorite) |
| Risk sınıflandırma | Canlı trafik/üretim ölçümü (dönemsel denetim) |
| Doğrulama referansı | Spesifik dil detayı (`external-kod-ornekleri.md`) |

## 5. Risk Sınıflandırma (envanter notu için)

| Seviye | Kriter | Örnek |
|--------|--------|-------|
| R-Yüksek | Üçüncü taraf ToS ihlal/sonlandırma riski açıkça yazılı | YouTube API Services ToS kısıtlama maddeleri |
| R-Orta | Agresif rate limit veya sıkı imza kuralı | MusicBrainz 1 çağrı/sn; Discogs User-Agent zorunlu; SoundCloud token limitleri |
| R-Düşük | Standart OAuth/key akışı, belirsizlik az | Spotify PKCE; Last.fm key+secret |
| R-Bilinmiyor | Kaynak doğrulanamadı | `⚠️ VERIFICATION REQUIRED` |

## 6. Kim Neyeye Karar Verir

| Konu | Karar mercii | Bu klasörün rolü |
|------|--------------|------------------|
| Sistem kullanılsın mı? (kapsam) | Üst otorite (Vault Steward) | Kanıt + envanter satırı hazırlar |
| Durum `aktif`/`beklemede` değişimi | Üst otorite | Öneri + gerekçe sunar |
| Adaptör kod tasarımı | Mimari (K013 child) | Şema/sınır referansı verir |
| Kayıt şeması alanı ekleme | Vault Steward onayı | Taslak + örnek üretir |
| Kanıt yenileme (bayatlık) | Otomatik görev (dönemsel) | `external-dogrulama.md` katmanı |

## 7. Örnek Bir Kayıt (şemanın uygulanması)

```yaml
# Şema örneği — gerçek kayıt external-sistem-envanteri.md içindedir
sistem: "Spotify"
saglayici: "Spotify AB"
kimlik_tipi: "OAuth 2.0 Authorization Code + PKCE (S256)"
api_surumu: "Web API (developer.spotify.com)"
durum: "beklemede"          # karar üst otoritede
uc_nokta: "https://api.spotify.com"
risk_notu: "rate limit hesap/anahtar bazlı"
kanit_url: "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"
dogrulama_tarihi: "2026-10-16"
not: "RFC 7636 ile uyumlu akış resmi tutorial'da doğrulandı"
```

**Secret uyarısı:** Yukarıdaki örnek **salt şemadır**; gerçek `client_secret`,
`refresh_token`, JWT özel anahtarı gibi değerler bu veya hiçbir `.md` dosyasına
yazılmaz (P5). Ortam değişkeni / secret store kullanılır.

## 8. §Açık Sorular

| # | Soru | Durum |
|---|------|-------|
| 1 | 1. dalga dış sistem listesi (aktif/beklemede dağılımı) | ⏳ Üst otorite kararı bekliyor |
| 2 | YouTube Music kapsam kesinliği (ToS "e.g." notu) | ⏳ Kanıt sunuldu; karar üst otoritede |
| 3 | Spotify/Deezer canlı kayıt akışı testi | ⚠️ VERIFICATION REQUIRED (canlı test yapılmadı) |

## 9. Kayıt Yaşam Döngüsü (state machine)

```text
[aday] --kanıt çekildi (web-arastirma §2)--> [beklemede]
[beklemede] --üst otorite onayı--> [aktif]
[beklemede] --üst otorite ret + gerekçe--> [kapsam-dışı]
[aktif] --kanıt >90 gün--> [bayat] --L1 yeniden çek--> [aktif] / [bilinmiyor]
herhangi --kaynak doğrulanamadı--> [bilinmiyor]  (⚠️ VERIFICATION REQUIRED etiketli)
```

| Geçiş | Tetikleyici | Yetkili |
|---|---|---|
| aday → beklemede | Envanter satırı + en az 1 çekilmiş kanıt URL | Vault yazıcısı (satır) |
| beklemede → aktif | Kapsam kararı | Üst otorite |
| beklemede → kapsam-dışı | Ret + gerekçe kaydı | Üst otorite |
| aktif → bayat | Kanıt tarihi >90 gün (§7 doğrulama takvimi) | Otomatik (L1) |
| * → bilinmiyor | Kaynak erişilemez/çelişkili | Doğrulama katmanı |

**Kural:** `aktif` yazımı hiçbir subagent/kod tarafından yapılmaz (P4); dosyada
yalnız üst otorite kararı geçerli olur.

## 10. Alan Bazlı Geçerlilik Kuralları (L2'nin hukuki temeli)

| Alan | Geçerlilik kuralı | İhlal sonucu |
|---|---|---|
| sistem adı | boş değil, benzersiz (envanter içi) | satır geçersiz |
| sağlayıcı | sağlayıcının kendi developer alanı (alan adı) | satır geçersiz |
| kimlik tipi | envanter sözlüğü ile tutarlı (OAuth2 PKCE / JWT-ES256 / API key / imza / yok) | satır geçersiz |
| API sürümü | sürüm/revizyon ya da `UNKNOWN` | `UNKNOWN` notu zorunlu |
| durum | yalnız 4 değerden biri (§3 sözlük) | satır geçersiz |
| uç nokta | https:// biçiminde; doğrulanamamışsa "referans" notu + ⚠️ | not yoksa satır geçersiz |
| risk notu | en az 1 risk cümlesi ("belirsiz" kabul edilmez) | satır geçersiz |
| kanıt URL | ≥1 URL; hepsi 2026-10-16 gibi tarihli çekimle doğrulanmış | P2 ihlali |
| doğrulama tarihi | `YYYY-MM-DD`; >90 gün → bayat (§9) | L1 görevi |
| not | boş olabilir (`-`) | — |

**Çifte kontrol:** Bu kurallar hem `external-sistem-envanteri.md` §10'da yazıcı
tarafından, hem `external-dogrulama.md` L2/GATE-EXT-2'de kapı tarafından denetlenir.

## 11. İlişkili Düğümler

| Düğüm | İlişki | Ne alır / ne verir |
|---|---|---|
| `K013-integraion/integration-master.md` | üst iddia kaynağı | §4 iddiaları, §7 açık sorular → §8 bu dosyada |
| `K013-integraion/api-contracts/openapi/` | kalıp aile | anayasa/dogrulama/kod-ornekleri/web-arastirma kalıp biçimleri |
| `external-systems/external-sistem-envanteri.md` | şema uygulaması | §3 şeması → satırlar |
| `external-systems/external-web-arastirma.md` | kanıt | URL çekimleri → kanıt alanı, P2/P7 |
| `external-systems/external-dogrulama.md` | kapı | §6 karar mercii → L4 ön koşulu |
| `external-systems/external-kod-ornekleri.md` | örnek | §5 risk + P5 → PLACEHOLDER sözleşmesi |
| `external-systems/index.md` | künye | children/grup/kapı listesi |

## 12. Değişiklik Kuralları (bu anayasada)

1. Yeni **ilk** ekleme: Vault Steward onayı + `index.md` §GRUPLAR hizası.
2. Yeni **alan** ekleme (kayıt şeması): tüm envanter satırları + L2 kapısı
  **aynı commit'te** güncellenir (yarım şema yasak).
3. Durum sözlüğüne değer ekleme: üst otorite onayı (§6).
4. Bu dosyada `⚠️ VERIFICATION REQUIRED` yalnızca madde bazında olur;
  madde kapatılınca etiket satır ile birlikte kaldırılır (etiket kalıcı yapılmaz).

## 13. Anti-Pattern'ler (bu klasörde yapılmaz)

| # | Anti-pattern | Sonuç | Doğru davranış |
|---|---|---|---|
| A1 | Kanıtsız envanter satırı ("Spotify kullanıyoruz, kaynak sonra") | P2 ihlali | Önce L1 çekimi, sonra satır |
| A2 | Subagent'ın `durum: aktif` yazması | P4/§6 ihlali | `beklemede` + üst otoriteye sunum |
| A3 | `.md`'ye gerçek `client_secret`/`.p8`/token yapıştırma | P5 ihlali → GATE-EXT-4 | `PLACEHOLDER` + secret store |
| A4 | Uç noktayı "galiba şu" diye yazma | Zero-Hallucination ihlali | doğrula ya da `⚠️ VERIFICATION REQUIRED` |
| A5 | Envanter satırı eksik alanla kaydetme | L2 fail (GATE-EXT-2) | 10 alan zorunlu (§3) |
| A6 | Bayat kanıtı güncel sanma (>90 gün) | P7 ihlali | L1 yeniden çekim |
| A7 | Adaptör kodunu bu klasöre koyma | P3 ihlali | child `adapters/` |
| A8 | Klasör adını "integration" düzeltmeye çalışma | İMLA dokunulmazlık ihlali | `integraion` aynen kalır |
| A9 | Elenen kaynağı sessizce unutmak | İzlenebilirlik kaybı | web-arastirma §5'e nedeniyle yaz |
| A10 | Kesinlik notunu karar sanmak ("ToS riskli → yasak") | §6 yetki aşımı | kanıt sun, kararı üst ver |

## 14. Sözlük (bu dosyaya özgü terimler)

| Terim | Tanım |
|---|---|
| Envanter | Dış sistemlerin 10 alanlı kayıt kümesi (bu klasör) |
| Kayıt şeması | §3'teki zorunlu alan listesi |
| Durum sözlüğü | `aktif/beklemede/kapsam-dışı/bilinmiyor` kümesi (§3) |
| Kapsam kapısı | "Entegre edilecek mi?" sorusunun üst otoriteye sevki (P6) |
| Kanıt URL | Çekilerek doğrulanmış resmi kaynak (P2) |
| Bayat kanıt | Doğrulama tarihi >90 gün olan kanıt |
| PLACEHOLDER | `.md` içinde secret yerine geçen dize (P5) |
| L1–L4 | Doğrulama katmanları (erişilebilirlik/şema/sözleşme/canlı) |
| GATE-EXT-* | CI kapıları (exit-code sözleşmesi, doğrulama §6) |
| R-seviye | Risk sınıfı: Yüksek/Orta/Düşük/Bilinmiyor (§5) |
| Hub-and-spoke | Merkez + uç sağlayıcı topolojisi (4. beyan; kanıt web-arastirma §3.4) |

## 15. Uzun Örnek — Tam Bir Envanter Kaydı (şemanın gösterimi)

```yaml
# external-anayasa §3 şemasının tam uygulanmış hâli (örnek satır)
# NOT: değerler external-sistem-envanteri.md §4'teki doğrulanmış bilgilerdir.
sistem: "SoundCloud"
saglayici: "SoundCloud (developers.soundcloud.com)"
kimlik_tipi: "OAuth 2.1 + PKCE (Authorization Code / Client Credentials)"
api_surumu: "API Guide — OpenAPI JSON/YAML (release'te güncellenir)"
durum: "beklemede"                # 'aktif' yalnız üst otoritece yazılır (§6)
uc_nokta: "https://api.soundcloud.com"
risk_notu: >
  Token ~1 saat; refresh TEK KULLANIMLIK; client-credentials kotaları
  50/12s (app) + 30/1s (IP); tüm client'lar şu an confidential (secret şart).
kanit_url:
  - "https://developers.soundcloud.com/docs"
dogrulama_tarihi: "2026-10-16"
not: >
  Akış adımları external-web-arastirma.md §11.5'te; canlı L4 testi yapılmadı
  (doğrulama §8 kısıt 1).
```

**Denetim izi (bu örnek satır için):** kanıt var mı ✅ (§2 #14) · 10 alan tam mı ✅
· durum kararı kimin? üst otorite ✅ (beklemede) · secret yok mu ✅ (PLACEHOLDER)
· tarih taze mi ✅ (2026-10-16).

## 16. Hızlı Referans Kartı (tek bakışta)

| Soru | Cevap | Kaynak |
|---|---|---|
| Envanter satırı kaç alanlı? | 10 alan zorunlu | §3 |
| `durum: aktif`'i kim yazar? | Yalnız üst otorite | §6 |
| Kanıt bayat eşiği? | 90 gün | §9 / doğrulama §7 |
| Secret ne olarak yazılır? | `PLACEHOLDER` | P5 |
| Risk seviyeleri? | R-Yüksek/Orta/Düşük/Bilinmiyor | §5 |
| Kod nereye gider? | child `adapters/` (bu klasörde değil) | §4 / P3 |
| Klasör adı düzeltilebilir mi? | Hayır (`integraion` dokunulmaz) | P8 |
| Elenen kaynak ne olur? | web-arastirma §5'e nedeniyle yazılır | A9 |
| Yeni sistem nasıl eklenir? | L1 çek → satır yaz → `beklemede` | §3, §9 |
| İddia ile karar ayrımı? | Kanıt sunulur, karar üst otoritede | §6, A10 |

## 17. Bu Dosyanın Öncelik Sırası (çelişki durumunda)

1. Zero-Hallucination (P1) — her şeyin üstünde.
2. Secret yokluğu (P5) — güvenlik, geçici eklemeye kapalı.
3. Karar mercii (§6 / P4) — yetki ayrımı.
4. Şema + kanıt (§3 / P2) — bütünlük.
5. Biçim/şablon tercihleri (iskelet, tablo düzeni) — en son.

**Çelişki kuralı:** Bu dosya ile `.ai/CLAUDE.md` (anayasa/guardrails) çelişirse
`.ai/CLAUDE.md` kazanır (vault-içi otorite sırası); bu dosya ile
`integration-master.md` çelişirse iddia/kanıt durumu için web-arastirma §6,
yapı için bu dosya esastır — ve fark üst otoriteye raporlanır.

## 18. Bu Anayasanın Kapsam Sonu (checklist)

- [x] İlkeller P1–P8
- [x] Kayıt şeması 10 alan + durum sözlüğü (§3)
- [x] Kapsam sınırı (§4) + karar mercii (§6)
- [x] Risk sınıfı (§5) + yaşam döngüsü (§9)
- [x] Geçerlilik kuralları (§10) + anti-pattern'ler (§13)
- [x] Örnek kayıt (§7, §15) + hızlı referans (§16) + öncelik sırası (§17)
- [ ] Kapsam kararı (`aktif` listesi) → üst otorite (§8 madde 1)

## §Açık Sorular (bu dosyanın kendi kapıları)

| # | Soru | Durum | Kapı |
|---|---|---|---|
| 1 | 1. dalga `aktif` listesi | ⏳ üst otorite | §6, §8.1 |
| 2 | YouTube Music kapsam kesinliği | ⏳ kanıt sunuldu, karar üst | §8.2 |
| 3 | Spotify/Deezer canlı akış | ⚠️ VERIFICATION REQUIRED | §8.3 |
| 4 | Kayıt şemasına yeni alan (ör. "sahip" alanı) | açık — Vault Steward onayı | §12.1 |
| 5 | `bilinmiyor` durumundaki sistem kalmış mı? (Deezer uç nokta) | ⚠️ bekliyor | envanter §5, §13 |

## §Kısa Yol (bir bakışta karar ağacı)

```text
Yeni dış sistem mi?
  ├─ Kanıt URL çekildi mi?  HAYIR → önce web-arastirma L1 (P2)
  ├─ 10 alan tam mı?        HAYIR → L2 fail, satırı düzelt (§3/§10)
  ├─ Secret değeri var mı?  EVET  → PLACEHOLDER'a çevir (P5)
  ├─ Durum 'aktif' yazacaksın? → DUR: üst otoriteye sor (§6)
  ├─ Kod yazacaksın?        → child adapters/ (P3)
  └─ Hepsi tamam            → satırı kaydet, index'e ekle, commit'i orkestratöre bırak
```

## §GRUPLAR

| Grup | Bu dosyadaki karşılığı |
|---|---|
| mimari | İlkeler (§2), kayıt şeması (§3), kapsam sınırı (§4), karar mercii tablosu (§6), yaşam döngüsü (§9), anti-pattern'ler (§13), sözlük (§14), örnek kayıt (§15) |
| kod | YOK — kod örnekleri `external-kod-ornekleri.md` (yalnız PLACEHOLDER) |
| kanit | Kanıt zorunluluğu (P2, P7) ve doğrulama tarihli kayıt (§3); denetim izi (§15) |

## §Kaynak

| Kaynak | Tarih | Erişim |
|---|---|---|
| `K013-integraion/integration-master.md` (§4 iddialar, §7 açık sorular) | 2026-10-12 | disk |
| `api-contracts/openapi/openapi-anayasa.md` (kalıp anayasa) | kalıp | disk |
| `external-systems/external-web-arastirma.md` (14 kaynak) | 2026-10-16 | disk |
| RFC 7636 (kimlik akışı referansı) | 2026-10-16 | https://www.rfc-editor.org/rfc/rfc7636 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
