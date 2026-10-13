---
title: "K018-devops/artifact-management — Konteyner İmaj ve Dockerfile Yönetimi: Katman, Cache, Multi-stage"
node_id: K018-devops/artifact-management/konteyner-imaj-yonetimi
node_type: module
status: draft
parent_id: K018-devops/artifact-management
children: []
depends_on:
  - K018-devops
  - K018-devops/artifact-management
evidence:
  - "Dockerfile glob = 0 dosya, docker-compose glob = 0 dosya (node_modules/.git disinda; 2026-10-12 olcum)"
  - ".ai/.agents/devops-engineer.md — Satır 96 (multi-stage, root olmayan kullanıcı, pin'li imaj), Satır 186 (VERIFICATION REQUIRED), Satır 187 (compose PLANNED), Satır 248 (gerekce metni) (2026-10-12)"
  - ".ai/architecture/K000-isletim-sistemi/os-master.md — 'K018' ve 'konteyner' dizgesi YOK (2026-10-12 tarama; atif hedefi dogrulanamadi)"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-anayasa.md — Satır 233 sahiplik atfi (K018-devops) (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 92-98 Composer onbellek (key/restore-keys), Satır 149-155 setup-node + npm cache (2026-10-12)"
validation:
  - "Dosya satir sayilari [IO.File]::ReadAllLines ile olcüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa basligi ile dondu (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada cakisma 0 (2026-10-12)"
  - "Glob kaniti: DOCKERFILE_COUNT=0, COMPOSE_COUNT=0 (2026-10-12)"
open_questions:
  - "Konteynerlestirme karari verilmedi — Dockerfile yok, compose yok (UNKNOWN)"
  - "Base imaj tercihi (distro) secilmedi (⚠️ VERIFICATION REQUIRED)"
  - "Imaj registry secimi (GHCR/diger) yapilmadi (⚠️ VERIFICATION REQUIRED)"
  - "devops-engineer.md profil envanteri (Satır 185) bayat: workflow dosyaları mevcut (index.md Satır 22) — profil guncellenmeli"
group: kod
---

# K018-devops/artifact-management/konteyner-imaj-yonetimi

> **ELI10:** Konteyner (container), uygulamayı ve bağımlılıklarını taşınabilir bir kutuya
> (imaj) paketleme yöntemidir. Bu depoda **hiçbir Dockerfile yok** (glob = 0). Bu dosya iki
> şeyi yapar: depodaki gerçeği kayda geçirir ve Dockerfile **geldiğinde** geçerli olacak
> kuralları tanımlar. "Docker ile deploy ediyoruz" cümlesi bu depoda yazılamaz.

## §1 Amaç

Bu modül, CoreMusic'in **konteyner imaj yönetimi kurallarının tek yetkili tanımıdır**. Beş karar
bu dosyada verilir: Dockerfile yokluğu gerçeği · dosya kuralları · katman/cache · multi-stage ·
imaj sürümleme. Konteyner kanıtı olmadan konteyner iddiası yazılmaz (§4).

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Depoda Dockerfile var mı? | Glob gerçeği | §4 |
| 2 | Dockerfile nasıl yazılır? | Dosya kuralları | §5 |
| 3 | Build nasıl hızlanır? | Katman + cache | §6 |
| 4 | İnce imaj nasıl üretilir? | Multi-stage | §7 |
| 5 | Mimari taşınabilirliği? | Multi-platform | §8 |
| 6 | İmaj nasıl etiketlenir? | Sürümleme | §9 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Dockerfile | adet | 0 | glob 2026-10-12 |
| H2 | docker-compose | adet | 0 | glob 2026-10-12 |
| H3 | Base imaj pinleme | kural | `:sürüm` zorunlu (§5.3) | bu dosyada tanımlı |
| H4 | Root olmayan kullanıcı | kural | `USER` zorunlu (§5.4) | bu dosyada tanımlı |
| H5 | Multi-stage | kural | Zorunlu (§7.1) | bu dosyada tanımlı |
| H6 | Üretim imajı | imaj | tanımı yok | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Dockerfile | ✅ Kurallar + yokluk gerçeği | ❌ Uygulama kodu → varlık kodu |
| İmaj cache | ✅ Katman sıralaması | ❌ CI onbelleği → `ci/` |
| Multi-stage | ✅ Aşama tasarımı | ❌ Derleme araçları → build alanı |
| Registry | ✅ Sürümleme kuralı | ❌ Registry hesabı → altyapı kararı |
| Container güvenlik | ✅ Sınır (rootless) | ❌ Güvenlik politikası → K009-security |
| Compose/çoklu servis | ✅ Yokluk + plan etiketi | ❌ Servis orkestrasyonu → gelecek karar |
| OS konteyner kavramı | ✅ Atıf sınırı | ❌ Namespace/cgroup → K000-isletim-sistemi |

**Sahiplik kararı (atıf ile):** konteyner, CI/CD ve deploy'un sahibi K018-devops'tur
(`linux-anayasa.md` Satır 233). OS kavramı (namespace/cgroup) K000 kapsamındadır.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| İmaj | image | Değiştirilemez paket | §9 |
| Katman | layer | İmajın değişmez dilimi | §6 |
| Cache | build cache | Katman önbelleği | §6.1 |
| Multi-stage | multi-stage build | Aşamalı build | §7 |
| Multi-platform | multi-platform build | Çok mimari imaj | §8 |
| Tag | tag | İnsan-okunur etiket | §9.1 |
| Digest | digest | Değişmez imaj kimliği | §9.2 |
| Base image | base image | Üstüne kurulan imaj | §5.3 |
| Rootless | root olmayan | `USER` ile yetki düşürme | §5.4 |
| Dockerfile | Dockerfile | İmaj tarifi | §5 |
| Compose | docker-compose | Çoklu servis tanımı | §4.2 |
| DI konteyneri | DI container | Farklı kavram (php-di) | §3.1 |
| Konteyner (OS) | OS container | Namespace/cgroup | K000 sınırı |

### §3.1 "Konteyner" sözcüğü — iki farklı kavram

| # | Kavram | Anlamı | Repo karşılığı | Kanıt |
|---|---|---|---|---|
| 1 | OS konteyneri | İzole çalışma ortamı | Yok | glob = 0 (§4.1) |
| 2 | DI konteyneri | Bağımlılık enjeksiyonu kaydı | `php-di ^7.0` | ADR-015 Satır 33 |
| 3 | Karışıklık riski | İkisi aynı sözcüğü paylaşır | — | Bu tablo |
| 4 | Yazım kuralı | "konteyner" tek başına = OS konteyneri | DI ise "DI konteyneri" | Kural |

**Kural:** bu dosyadaki "konteyner" daima **OS konteyneri** anlamındadır; DI kaydı asla bu
dosyanın konusu değildir.

## §4 Repo gerçeği — konteyner kanıtı yok

### §4.1 Glob kanıtı (2026-10-12)

| # | Desen | Sonuç | Filtre | Sonuç |
|---|---|---|---|---|
| 1 | `Dockerfile*` | 0 | `node_modules`, `.git` hariç | 0 |
| 2 | `docker-compose*` | 0 | Aynı filtre | 0 |
| 3 | Konteyner imaj tarifi | — | — | Yok |
| 4 | Compose hizmet tanımı | — | — | Yok |
| 5 | `.dockerignore` | 0 | Aynı filtre + `-Force` | Yok |

### §4.2 Vault envanteri

| # | Kayıt | Dosya / Satır | Etiket | Not |
|---|---|---|---|---|
| 1 | Docker / multi-stage | devops-engineer.md / Satır 186 | ⚠️ VERIFICATION REQUIRED | "glob ile doğrulanmalı" |
| 2 | docker-compose | devops-engineer.md / Satır 187 | ⚠️ PLANNED | Spesifikasyonda |
| 3 | Gerekçe metni | devops-engineer.md / Satır 248 | — | Kanıt yoksa iddia yazılmaz |
| 4 | Docker kuralları | devops-engineer.md / Satır 96 | Görev tanımı | Multi-stage + rootless + pin |
| 5 | Sahiplik atfı | linux-anayasa.md / Satır 233 | — | "CI/CD, konteyner, deploy → K018" |
| 6 | Atıf hedefi | os-master.md (tarama) | ⚠️ Doğrulanamadı | "K018"/"konteyner" dizgesi YOK |

### §4.3 Bayat kayıt uyarısı (profil envanteri)

| # | Profil satırı | İddia | Karşı kanıt | Sonuç |
|---|---|---|---|---|
| 1 | devops-engineer.md / Satır 185 | "GitHub Actions workflow'ları ⚠️ PLANNED, dosya yok" | `.github/workflows/` = 2 dosya (index.md Satır 22) | Bayat |
| 2 | devops-engineer.md / Satır 184 | Issue template IMPLEMENTED | `.github/ISSUE_TEMPLATE/**` mevcut | Tutarlı |
| 3 | Sonuç | Profil envanteri tek seferlik | Tarama tazelenmeli | Açık (§15) |

**Kural:** bayat envanter satırı **silinmez, düzeltilir**; düzeltme vault sahibine aittir ve
bu dosyada yalnızca tespit kayda geçer (onaysız vault yazımı yasak).

### §4.4 Mevcut build/dağıtım gerçeği (konteynersiz)

| # | Katman | Gerçeği | Kanıt | Durum |
|---|---|---|---|---|
| 1 | CI çalıştırıcı | `ubuntu-latest` | ci.yml Satır 27, 144 | VM, konteyner değil |
| 2 | PHP kurulumu | `setup-php` | ci.yml Satır 34 | Host'a kurulum |
| 3 | Node kurulumu | `setup-node` + npm cache | ci.yml Satır 149-152 | Host'a kurulum |
| 4 | Composer cache | `actions/cache` | ci.yml Satır 92-98 | Dosya önbelleği |
| 5 | Deploy | tanımsız | — | ⚠️ VERIFICATION REQUIRED |

## §5 Dockerfile kuralları (kanıt geldiğinde geçerli)

### §5.1 Dosya yerleşimi kuralları

| # | Kural | Zorunlu mu | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Tek Dockerfile, kökte | evet | Tek gerçek | `Dockerfile` yolu |
| 2 | `.dockerignore` | evet | `node_modules` sızıntısı | Dosya varlığı |
| 3 | Dosya adı büyük harf | evet | Standart ad | `Dockerfile` |
| 4 | Compose ayrı dosyada | evet (varsa) | Ayrışım | `docker-compose.yml` |
| 5 | Dockerfile vault'a kayıtlı | evet | Görsellik | index envanteri |

### §5.2 Talimat (instruction) sırası

| # | Sıra | Talimat | Amaç | Cache etkisi |
|---|---|---|---|---|
| 1 | 1 | `FROM` | Base imaj | Katman 1 |
| 2 | 2 | `WORKDIR` | Dizin | Küçük |
| 3 | 3 | Bağımlılık kopyası | Lock dosyaları | Değişince yeniden |
| 4 | 4 | Bağımlılık kurulumu | `install` | Büyüyen katman |
| 5 | 5 | Uygulama kodu | `COPY .` | Sık değişir → sona |
| 6 | 6 | Build/adım | Derleme | Orta |
| 7 | 7 | `USER` | Yetki düşürme | §5.4 |
| 8 | 8 | `CMD`/`ENTRYPOINT` | Başlangıç | Küçük |

**Kural:** sık değişen katmanlar (kod) **geç katmanda** durur; nadir değişenler (bağımlılık)
**erken katmanda** — tersi her build'i baştan çalıştırır (§6.1).

### §5.3 Base imaj pinleme

| # | Kural | İyi | Kötü | Gerekçe |
|---|---|---|---|---|
| 1 | Sürüm zorunlu | `php:8.4` | `php:latest` | Tekrarlanabilirlik |
| 2 | Minor kilidi tercih | `php:8.4` | `php:8` | Sürpriz azaltma |
| 3 | Sürücü notu | Yorumda gerekçe | Sessiz pin | Görünürlük |
| 4 | Yükseltme kararı | İnceleme + test | Otomatik | Regresyon |
| 5 | Digest kilidi | Opsiyonel | — | §9.2 |

**Repo bağı:** PHP sürümü zaten sabittir: `PHP_VERSION: '8.4'` (ci.yml Satır 22; gerekçe
Satır 22 yorumu: üç composer.json `"php": ">=8.4"`). Base imaj seçimi bu sabitle
**aynı kalmak zorundadır**.

### §5.4 Rootless (root olmayan kullanıcı)

| # | Kural | Zorunlu mu | Kanıt | Gerekçe |
|---|---|---|---|---|
| 1 | `USER` talimatı | evet | devops-engineer.md Satır 96 | Yetki düşürme |
| 2 | Sistem kullanıcısı | evet | Standart (`www-data` vb.) | İsimli kullanıcı |
| 3 | Yazma izni | evet | `COPY --chown` | Uygulama yazabilmeli |
| 4 | Port < 1024 | İzinli | `setcap`/köprü port | Belge gerekir |
| 5 | Root ile tek build adımı | Nadir | Yorum zorunlu | İstisna gerekçesi |

### §5.5 Tek başlangıç kuralı

| # | Kural | Gerekçe | Denetim | Not |
|---|---|---|---|---|
| 1 | Tek `ENTRYPOINT` **veya** tek `CMD` | Belirsiz başlangıç | Talimat sayımı | — |
| 2 | `ENTRYPOINT` + `CMD` birlikte | Yalnız sözcük-dizisi değişimi | İkisi varsa inceleme | Kabul |
| 3 | Shell formundan kaçınma | Sinyal işleme | `exec` formu | §13 risk 5 |
| 4 | Başlangıç testi | Konteyner ayağa kalkar mı | `healthcheck` | §5.6 |

### §5.6 `HEALTHCHECK` kuralı

| # | Kural | Zorunlu mu | Amaç | Not |
|---|---|---|---|---|
| 1 | `HEALTHCHECK` tanımlı | evet (konteyner varsa) | Sağlık sinyali | Orkestratör girişi |
| 2 | Zaman aralığı | evet | Gereksiz yük yok | Bütçe: `performance/` |
| 3 | Üst sınır | evet | Asılı sağlık yok | Aşım = sağlıksız |
| 4 | Uygulama uç noktası | tercih | Gerçek sağlık | — |
| 5 | Basit komut | tercih | Bağımlılıksız | Dosya varlığı kontrolü |

## §6 Katman (layer) ve cache kuralları

### §6.1 Cache temel kuralı

| # | Kural | Neden | Etki | Kanıt |
|---|---|---|---|---|
| 1 | Katman üstte değişmezse yeniden kullanılır | Docker cache | Hızlı build | Docker build cache |
| 2 | `COPY` değişirse sonraki katmanlar yeniden | Önbellek kırılır | Yavaş build | Aynı kaynak |
| 3 | Bağımlılık lock dosyası ayrı kopyalanır | Sık değişmez | Büyük kazanç | §5.2 satır 3 |
| 4 | Cache sıfırlama (`--no-cache`) yalnız krizde | Normalde gerekmez | Bilinçli | Kural |

### §6.2 Kötü ve iyi sıralama

| # | Durum | Sıralama | Sonuç | Karar |
|---|---|---|---|---|
| 1 | Kötü | `COPY .` sonra `composer install` | Her kod değişikliğinde bağımlılık yeniden | Yasak |
| 2 | İyi | `composer.lock` kopyala → install → `COPY .` | Kilit değişmezse install cache | Zorunlu |
| 3 | Kötü | `RUN` komutları tek tek küçük katman | şişkin imaj | Kaçın |
| 4 | İyi | Mantıksal olarak birleşik `RUN` | Temiz imaj | Tercih |
| 5 | Kötü | Önbelleğe alınan paket artıkları | şişkin imaj | `clean` ekle |

### §6.3 Cache matrisi — ne değişince ne yenilenir

| # | Değişen | Yenilenen katman | Maliyet | Önlem |
|---|---|---|---|---|
| 1 | `composer.lock` | Bağımlılık kurulumu + sonrası | Yüksek | Kilit bilinçli güncelleme |
| 2 | Uygulama kodu | Kod katmanı + sonrası | Orta | §5.2 sıra |
| 3 | Base imaj | Her şey | Çok yüksek | Pin + bilinçli yükseltme |
| 4 | `USER`/`ENV` | Sonraki her şey | Orta | Sonda tut |
| 5 | Compose tanımı | İmaj değişmez | — | Yeniden build yok |

### §6.4 Cache doğrulama kaydı (konteyner geldiğinde)

| # | Alan | Zorunlu | Değer örneği | Amaç |
|---|---|---|---|---|
| 1 | Build süresi (temiz) | evet | Ölçüm değeri | Baz |
| 2 | Build süresi (cache'li) | evet | Ölçüm değeri | Kazanç |
| 3 | Kazanç yüzdesi | evet | Hesap değeri | §6.1 |
| 4 | Kırılan katman | evet | Bağımlılık/kod | §6.3 |
| 5 | İmaj boyutu | evet | Ölçüm değeri | §7.2 |
| 6 | Katman sayısı | evet | Sayım | §6.2 satır 3-4 |

**Kural:** cache kazancı ölçülmeden "hızlı build" iddiası yazılmaz (ölçüm yoksa iddia yok);
ölçüm, `performance/` protokolüne göre ≥ 3 tekrarla alınır.

## §7 Multi-stage (çok aşamalı build)

### §7.1 Aşama kuralları

| # | Aşama | İçerik | Amaç | Çıktı |
|---|---|---|---|---|
| 1 | Builder | Derleme araçları | Build | Ara artefakt |
| 2 | Runtime | Yalnız çalışma gerekleri | İnce imaj | Son imaj |
| 3 | Araç aşaması | Derleme aracı ayrı | Güvenlik | — |
| 4 | Test aşaması | CI'da opsiyonel | Kapı | CI bağımlı |

### §7.2 Multi-stage fayda matrisi

| # | Fayda | Ölçüm | Kural | Not |
|---|---|---|---|---|
| 1 | İmaj boyutu | Araçlar dışarıda | Runtime'a yalnız artefakt | §7.1 satır 1-2 |
| 2 | Saldırı yüzeyi | Derleme aracı yok | Runtime temiz | K009 sınırı |
| 3 | Cache | Aşama içi katman | Aşamalar bağımsız | §6 |
| 4 | Karmaşıklık | Ek `COPY --from` | Belgele | Denetim |

### §7.3 Aşama geçişi kuralları

| # | Kural | Zorunlu mu | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Artefakt kopyası `COPY --from` ile | evet | Açık aktarım | Talimat incelemesi |
| 2 | Araç imajı da pin'li | evet | §5.3 | `FROM` incelemesi |
| 3 | Aşama sayısı asgari | evet | Bakım yükü | Sayım |
| 4 | Her aşamanın adı | tercih | Okunabilirlik | `AS builder` |

## §8 Multi-platform (çok mimari)

### §8.1 Platform kuralları

| # | Kural | Neden | Durum | Not |
|---|---|---|---|---|
| 1 | Hedef mimari yazılır | Belirsiz imaj | ⚠️ tanımsız | Mimari kararı gerekiyor |
| 2 | Platform bağımlılığı belgelenir | Uzantı/derleme farkı | Kural | Denetim |
| 3 | Mimari matrisi CI'da | Doğrulama | Kural | §8.2 |
| 4 | `latest`-etiketli çok mimari | Yanıltıcı | Yasak | §9.1 |

### §8.2 Mimari doğrulama matrisi

| # | Mimari | Kaynak | Doğrulama | Durum |
|---|---|---|---|---|
| 1 | `amd64` | CI ubuntu-latest | Mevcut koşu | ci.yml |
| 2 | `arm64` | — | Doğrulanmıyor | ⚠️ VERIFICATION REQUIRED |
| 3 | Uzantılar | — | Mimari duyarlılığı bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| 4 | Karar | — | Desteklenen mimari seti | ADR gerektirir |

## §9 İmaj sürümleme

### §9.1 Tag (etiket) kuralları

| # | Kural | İyi | Kötü | Gerekçe |
|---|---|---|---|---|
| 1 | Sürüm etiketi | `1.4.2` | `latest` | Tekrarlanabilirlik |
| 2 | Majör/minör/kilitleme | `1.4` yükseltilebilir | Sessiz sürpriz | Bilinç |
| 3 | Git bağlantısı | Tag → commit | Bağlantısız | İzlenebilirlik |
| 4 | Birden çok etiket | Sürüm + `stable` | Tek `latest` | Çift etiket serbest |
| 5 | Silinen etiket | Yasak | Etiket silme | Geri dönüşsüzlük |

### §9.2 Digest (değişmez kimlik)

| # | Kural | Kullanım | Artı | Eksi |
|---|---|---|---|---|
| 1 | Digest kilidi | Üretim | Mutlak tekrarlanabilirlik | Okunmaz |
| 2 | Tag + digest | Üretim | İkisi de | Uzun |
| 3 | Yalnız tag | Geliştirme | Kısa | Değişebilir |
| 4 | Yükseltme akışı | Digest → tag → test | Sıra | Kural |

### §9.3 Sürümleme karar matrisi

| # | Ortam | Kural | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Geliştirme | Tag | Hız | — |
| 2 | CI | Sürüm + commit etiketi | İz | CI |
| 3 | Üretim | Digest kilidi | Güvenlik | §9.2 satır 1 |
| 4 | Geri dönüş | Önceki digest | Rollback | `rollback/` |

### §9.4 İmaj kayıt tablosu (release başına)

| # | Alan | Zorunlu | Değer örneği | Denetim |
|---|---|---|---|---|
| 1 | İmaj adı | evet | Registry + yolu | §9.1 |
| 2 | Tag | evet | Sürüm etiketi | §9.1 satır 1 |
| 3 | Digest | evet (üretim) | `sha256` değeri | §9.2 satır 1 |
| 4 | Kaynak commit | evet | Commit kimliği | §9.1 satır 3 |
| 5 | Base imaj | evet | Pin'li sürüm | §5.3 |
| 6 | Mimari | evet | `amd64` | §8.2 |
| 7 | Tarama sonucu | evet | K009 çıktısı | §10 satır 3 |
| 8 | Boyut | evet | Ölçüm değeri | §6.4 |

**Kural:** kayıt satırı eksiksiz olmadan imaj dağıtıma giremez (release kapısı §17).

## §10 Güvenlik sınırı (K009'a devir)

| # | Konu | K018 (bu dosya) | K009 (devir) | Sınır |
|---|---|---|---|---|
| 1 | Rootless | Uygulama kuralı | Politika | §5.4 |
| 2 | Secret build arg | **Yasak** (§13) | Secret politikası | `secrets/` |
| 3 | İmaj taraması | Kapı kurma | Tarama politikası | K009 |
| 4 | İmza doğrulama | — | Politika | K009 |
| 5 | Base imaj denetimi | Pin (§5.3) | Kabul listesi | Ortak |

**Kural:** bu dosya konteynerin **yapısal** kurallarını verir; **güvenlik politikası** K009'un
yetkisindedir (K018/index.md Satır 19: secret-scan K009'a bağlı).

## §11 Dockerfile şablonu (öneri — repo'da uygulanmadı)

Aşağıdaki şablon **öneridir**; depoda uygulanmamıştır ve §5-§7 kurallarını gösterir:

```dockerfile
FROM php:8.4-cli AS builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

FROM php:8.4-cli AS runtime
WORKDIR /app
COPY --from=builder --chown=www-data:www-data /app /app
USER www-data
CMD ["php", "index.php"]
```

**Uyarı:** bu şablon **öneridir** (kural: §13 yasak 1 — kanıtsız "üretiliyoruz" cümlesi yok);
gerçekleşmesi için kullanıcı onayı ve ADR gerekir. Base imaj `php:8.4-cli`, ci.yml Satır 22'deki
`PHP_VERSION: '8.4'` sabitiyle uyumludur (§5.3 son satırı).

## §12 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | (glob) DOCKERFILE_COUNT | 0 | Dockerfile yokluğu |
| 2 | (glob) COMPOSE_COUNT | 0 | Compose yokluğu |
| 3 | .ai/.agents/devops-engineer.md | 96 | Docker kuralları görev tanımı |
| 4 | .ai/.agents/devops-engineer.md | 184-187 | Envanter etiketleri (IMPLEMENTED/PLANNED/VERIFICATION) |
| 5 | .ai/.agents/devops-engineer.md | 248 | Bilinçli `VERIFICATION REQUIRED` gerekçesi |
| 6 | .ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-anayasa.md | 233 | Konteyner sahipliği atfı |
| 7 | .ai/architecture/K000-isletim-sistemi/os-master.md | (tarama) | Atıf hedefinde dize YOK |
| 8 | .github/workflows/ci.yml | 22 | `PHP_VERSION: '8.4'` sabiti |
| 9 | .github/workflows/ci.yml | 27, 144 | `ubuntu-latest` çalıştırıcı |
| 10 | .github/workflows/ci.yml | 34 | `setup-php` (host kurulumu) |
| 11 | .github/workflows/ci.yml | 92-98 | Composer cache (key + restore-keys) |
| 12 | .github/workflows/ci.yml | 100-102 | `composer install` |
| 13 | .github/workflows/ci.yml | 149-155 | `setup-node` + npm cache + `npm ci` |
| 14 | .ai/architecture/K018-devops/index.md | 19 | Secret-scan sahipliği K009 (sınır) |
| 15 | .ai/architecture/K018-devops/index.md | 22, 24 | Workflow envanteri (2 dosya, gitleaks) |

## §13 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | "Docker deploy ediyoruz" demek | Kanıt yok (glob = 0) | §4.1 |
| 2 | `latest` base imaj | Tekrarlanamaz build | §5.3 satır 1 |
| 3 | Root ile çalışma | Yetki artışı | §5.4 |
| 4 | `ARG` ile secret geçmek | İmajda kalır | §10 satır 2 |
| 5 | Shell formu `CMD` | Sinyal işleme kaybı | §5.5 satır 3 |
| 6 | `COPY .` ilk katman | Cache her zaman kırılır | §6.2 satır 1 |
| 7 | Tek `latest` etiketi | İz kaybı | §9.1 satır 1 |
| 8 | Etiket silme | Geri dönüşsüzlük | §9.1 satır 5 |
| 9 | Konteyner = DI konteyneri karıştırma | Kavram kayması | §3.1 |
| 10 | Bayat profili doğru saymak | Yanlış envanter | §4.3 |

## §14 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K018-devops/pipeline | İmaj build adımı | CI adımı | — | ⚠️ VERIFICATION REQUIRED |
| 2 | K018-devops/deployment | İmaj dağıtımı | Yayılım girdisi | — | ⚠️ VERIFICATION REQUIRED |
| 3 | K018-devops/artifact-management | İmaj = artefakt | İmaj envanteri | Sürümleme | §9 |
| 4 | K018-devops/ci | Cache kuralları | Onbellek bütünlüğü | actions/cache | mevcut (konteynersiz) |
| 5 | K009-security | Rootless + tarama kapısı | Politika | Ortak denetim | devir (§10) |
| 6 | K000-isletim-sistemi | Namespace/cgroup | Kavram sınırı | Atıf | §4.2 satır 5-6 |
| 7 | K019-testing | İmaj içi test | Test ortamı | — | ⚠️ VERIFICATION REQUIRED |

## §15 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Dockerfile yok | Konteyner stratejisi yok | yüksek | Karar (ADR) | UNKNOWN |
| 2 | Compose yok | Çoklu servis yok | yüksek | Karar | PLANNED (profil 187) |
| 3 | Base imaj seçimi | Şablon eksik kalır | orta | İmaj kararı | ⚠️ VERIFICATION REQUIRED |
| 4 | Mimari desteği bilinmiyor | arm64 kırılabilir | orta | §8.2 doğrulama | ⚠️ VERIFICATION REQUIRED |
| 5 | Bayat profil envanteri | Yanlış envanter | orta | Profil düzeltmesi | §4.3 |
| 6 | os-master atfı doğrulanamadı | Bozuk referans | düşük | Atıf düzeltmesi | §4.2 satır 6 |
| 7 | Registry yok | İmaj dağıtımı yok | orta | Registry kararı | ⚠️ VERIFICATION REQUIRED |
| 8 | İmaj taraması yok | Güvenlik boşluğu | orta | K009 kapısı | K009 |

## §16 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Dosya | Dockerfile var/yok | Glob | her vault oturumu | §4.1 |
| 2 | Kurallar | §5 maddeleri uygulanır | Kod incelemesi | Dockerfile geldiğinde | §5 |
| 3 | Cache | Bağımlılık katmanı erken | İnceleme | Her değişiklik | §6.2 |
| 4 | İmaj | Sürüm + digest | İmaj listesi | Her release | §9 |
| 5 | Güvenlik | Rootless + secret yok | Tarama | Her build | §10 |
| 6 | Mimari | Destek listesi | §8.2 | Yıllık | ⚠️ VERIFICATION REQUIRED |
| 7 | Profil | Envanter taze | Vault denetimi | Aylık | §4.3 |

## §17 Kabul kriteri — Dockerfile depoya girdiğinde

| # | Kriter | Zorunlu | Doğrulayıcı | Red nedeni |
|---|---|---|---|---|
| 1 | Pin'li base imaj | evet | `FROM` satırı | `latest` |
| 2 | `USER` mevcut | evet | Talimat | Root çalıştırma |
| 3 | Multi-stage | evet | Aşama sayısı ≥ 2 | §7.1 |
| 4 | Sıra kuralı | evet | Bağımlılık önce | §6.2 |
| 5 | `.dockerignore` | evet | Dosya varlığı | Eksik |
| 6 | Compose kararı | evet | ADR/PLANNED etiketi | Sessiz |
| 7 | K009 tarama kapısı | evet | Ortak denetim | Yok |
| 8 | Şablon güncelleme | evet | Bu dosya §11 | Bayat şablon |

## §18 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Docker Build cache sayfası, build önbelleğini ve katman cache'ini tanımlar | https://docs.docker.com/build/cache/ | 2026-10-12 | HTTP 200 doğrulandı |
| Docker Building best practices sayfası, Dockerfile en iyi uygulamalarını tanımlar | https://docs.docker.com/build/building/best-practices/ | 2026-10-12 | HTTP 200 doğrulandı |
| Docker Multi-stage builds sayfası, çok aşamalı build'i tanımlar | https://docs.docker.com/build/building/multi-stage/ | 2026-10-12 | HTTP 200 doğrulandı |
| Docker Multi-platform builds sayfası, çok mimari imaj build'ini tanımlar | https://docs.docker.com/build/building/multi-platform/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 10 yeni
derin-branch dosyasında bulunmaz.

## §19 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık kaydedildi | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Dockerfile glob | `Get-ChildItem -Filter` | 0 dosya | 2026-10-12 |
| 5 | Compose glob | `Get-ChildItem -Filter` | 0 dosya | 2026-10-12 |
| 6 | os-master.md tarama | `Select-String` | "K018" 0 isabet | 2026-10-12 |
| 7 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 8 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 9 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 10 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 11 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
