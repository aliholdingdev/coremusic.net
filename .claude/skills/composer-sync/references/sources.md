# Kaynaklar (Truth Mode)

> Bu skill'deki her doküman-destekli iddia aşağıdaki resmi kaynaklardan
> doğrulanmıştır. Model hafızası bu listenin ALTINDADIR.
>
> Erişim tarihi: **2026-10-21** (her kaynak için aynı oturumda fetch edilmiştir).

---

## S1 — Composer Configuration Reference

- **URL:** https://getcomposer.org/doc/06-config.md
- **Erişim:** 2026-10-21
- **Aldığımız doğrulanmış alanlar:**
  - `platform-check` (default `true`, sadece PHP sürümü kontrol eder; `true` →
    ext'ler de kontrol edilir; `false` → `platform_check.php` üretilmez)
  - `optimize-autoloader` (varsayılan `false`; `true` → install/update sonunda
    optimize dump)
  - `strict-psr-autoloader` (varsayılan `false`; install/update'te PSR uyumsuz
    paketleri `exit 6` ile patlatır; `dump-autoload` → `exit 1`)
  - `classmap-authoritative` (default `false`; `true` → yalnız classmap'ten yükle)
  - `cache-files-maxsize` (default `1G`; `500M`, `300MiB` gibi değerler kabul eder)
  - `htaccess-protect` (varsayılan `true`)
  - `lock` (varsayılan `true`; `false` → lock dosyası güncellenmez)
- **Kullanıldığı bölümler:** SKILL.md §5.4, §9.1, §9.2, §9.4

---

## S2 — Composer CLI Reference

- **URL:** https://getcomposer.org/doc/03-cli.md
- **Erişim:** 2026-10-21
- **Aldığımız doğrulanmış alanlar:**
  - `install` davranışı: lock varsa lock'tan; yoksa `update` gibi çözümler
    (lock üretir)
  - `dump-autoload` seçenekleri: `--optimize` (classmap oluşturur),
    `--classmap-authoritative` (classmap'te olmayan sınıf yüklenmez),
    `--apcu`, `--dry-run`, `--no-scripts`
  - `update` seçenekleri: `--with-all-dependencies`, `<vendor>/<package>`
    hedefli güncelleme
  - `validate`: `--no-check-publish`, `--no-check-all` bayrakları
  - `config --list` / `config key value` yapılandırma düzenleme
- **Kullanıldığı bölümler:** SKILL.md §5.1, §7.3

---

## S3 — Microsoft Learn: Symbolic Links (junction vs symlink)

- **URL:** https://learn.microsoft.com/en-us/windows/win32/fileio/symbolic-links
- **Erişim:** 2026-10-21
- **Aldığımız doğrulanmış alanlar:**
  - Directory junction: `CreateSymbolicLinkW` ile `SYMBOLIC_LINK_FLAG_DIRECTORY`
    oluşturulur; yetki/Developer Mode gerektirmez
  - Symlink (reparse point): `SYMBOLIC_LINK_FLAG_ALLOW_UNPRIVILEGED_CREATE`
    ile Developer Mode'da oluşturulabilir
  - Junction hedefi mutlak path'tir; symlink göreli olabilir
  - Hedef volume çevrimdışıyken junction çalışmaya devam eder, symlink kırılır
- **Kullanıldığı bölümler:** SKILL.md §3.1
- **Ek kanıt (disk, 2026-10-21):**
  `Get-Item <sub>\vendor\coremusic\shared-infrastructure -Force` →
  `LinkType=Junction`, `Target=C:\www\coremusic.net\shared`
  (admin, api, auth, home subdomain'lerinde ölçüldü; media §3.2 komutuyla
  doğrulanır).

---

## Not: Doğrulanamayanlar

- PHP'nin `is_link()` junction'da `false` döndürmesi — Windows PHP kaynak
  davranışına dayanır; 2026-10-21 oturumunda PHP binary'si PATH'te olmadığı
  için **canlı doğrulanmadı** → SKILL.md §3.1 `VERIFICATION REQUIRED` etiketlidir.
- `php`/`composer` çıktı örnekleri — komutlar resmi dokümandan alınmıştır;
  canlı çalıştırma `NOT TESTED` işaretlidir (SKILL.md §6.2).
