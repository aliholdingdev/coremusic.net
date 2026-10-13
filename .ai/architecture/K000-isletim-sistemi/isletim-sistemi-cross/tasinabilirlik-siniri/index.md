---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children:
  - K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-surucu
  - K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri
  - K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
evidence:
  - "home.coremusic.net/.htaccess:26"
  - "home.coremusic.net/web.config:14"
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:35"
  - "https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options"
validation: "2026-10-13 · 1 yeni URL HEAD 200 · envanter (2633) ile kesişim 0 · repo satırları Select-Object -Index (N-1) ile okundu · index 108 → 98 (§1/§2 tabloları özet cümleye de-duplike edildi)"
open_questions:
  - "Q4 — hedef platform seti tek SSOT'ta tanımlı değil → UNKNOWN"
  - "Q-X51 — taşınabilirlik bütçesi (soyutlama başına maliyet) ölçülmedi → ⚠️ VERIFICATION REQUIRED"
  - "Q-X52 — Windows/macOS runner kanıtı yok (yalnız ubuntu-latest) → UNKNOWN"
group: mimari
---

# tasinabilirlik-siniri — Deep-Dal İndeksi (Kademe-4)

## §0 ELI15

| Soru | Kısa yanıt | Derin cevap |
|---|---|---|
| Kod taşınabilir mi? | Bazısı | `-anayasa` §1-§3 |
| Taşınmayan ne? | OS API, bayrak, dosya biçimi | `-anayasa` §4 |
| Soyutlama ne kadar pahalı? | Ölçülmedi | `-surucu` §5 |
| Bugünkü kanıt ne? | `.htaccess` + `web.config` aynı uygulama | `-ornekler` §1 |

## §1 Amaç

Bu alt-dalın beş amacı vardır — **sınır tanımla · yasaklı yüzeyi listele · soyutlama
maliyetini tarif et · karar akışını çalıştır · repo kanıtını göster**; her birinin
hangi dosyanın hangi bölümü olduğu §GRUPLAR ve §3 envanterinde tektir (dedup,
2026-10-13).

## §2 Kademe / rol

Kademe 4 (alt-dal) · ebeveyn `isletim-sistemi-cross` (Kademe-3). Kardeş ayrımı:
`platform-yuzeyleri` **fark ne**yi tanımlar, `derleme-ve-paketleme` **nasıl**
üretildiğini, bu alt-dal ise **kararı** verir — hangi kod taşınır, hangi taşınmaz,
soyutlama kaç para.

## §3 3 dosya envanteri (disk · 2026-10-13)

| # | Dosya | Omurga | group | Satır |
|---|---|---|---|---|
| 1 | `tasinabilirlik-siniri-anayasa.md` | §0-§9 (anayasa iskeleti) | mimari | ≥500 |
| 2 | `tasinabilirlik-siniri-surucu.md` | §0-§7 (surucu iskeleti) | mimari | ≥500 |
| 3 | `tasinabilirlik-siniri-ornekler.md` | §1-§5 (ornekler iskeleti) | kod | ≥500 |

## §4 §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Alt-dal künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `tasinabilirlik-siniri-anayasa.md` | Taşınabilirlik anayasası ve karar matrisi | Kademe-4 |
| mimari | `tasinabilirlik-siniri-surucu.md` | Uygulama katmanı, akış, hata modları, ölçüm | Kademe-4 |
| kod | `tasinabilirlik-siniri-ornekler.md` | Gerçek repo örnekleri ve kalıp gösterimi | Kademe-4 |

## §5 Komşu sınırı

| # | Dışarıda olan | Nereye gider | Neden |
|---|---|---|---|
| 1 | OS API fark matrisi | `platform-yuzeyleri` | fark orada tanımlanır; burada karar verilir |
| 2 | Derleme/paket araçları | `derleme-ve-paketleme` | araç zinciri orası |
| 3 | Platform türü karşılaştırması | `isletim-sistemi-turu` | aile içi sınıflandırma orada |
| 4 | Test koşumu / portability matrix | `isletim-sistemi-testing` | ölçüm tanımlanır, koşum orada |
| 5 | CI pipeline tasarımı | `K018-devops` | `ci.yml` yalnız kanıt olarak okunur |
| 6 | Çekirdek/AB kavramları · DSP/donanım | `isletim-sistemi-kernel` · `K004-dsp` · `K001-donanım` | kapsam dışı |

## §6 depends_on

| # | Bağımlılık | Tür | Ne alınıyor |
|---|---|---|---|
| 1 | `platform-yuzeyleri` | alt-dal | yüzey fark matrisi (girdi) |
| 2 | `derleme-ve-paketleme` | alt-dal | toolchain ve paket gerçekleri (girdi) |
| 3 | `../index.md` §1 · `../../os-master.md` §5 | kök + master | 4 yüzeyden 4.'sü · platform bağımlılığı omurgası |

> Çıkış: `depends_on` **2 alt-dal + 2 referans**; ters bağımlılık yok (çakışma = 0).

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X51 · Q-X52 · Q-X1 | FM `open_questions` alanında (tekrarlanmadı) | ⚠️ VERIFICATION REQUIRED / UNKNOWN |
| Q-X53 | macOS yüzeyi (CoreFoundation/`dyld`) üretimde kullanılıyor mu? | UNKNOWN |
| Q-X54 | "Taşınabilir" eşiği — %kaç ortak kod kabul edilir? | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
