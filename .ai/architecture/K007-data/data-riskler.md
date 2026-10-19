---
node_id: "K007-data/riskler"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-kapsam"
  - "K007-data/data-kararlar"
  - "K007-data/data-dogrulama"
evidence:
  - "shared/src/Repository/ApiKeyRepository.php:185-193 (soft delete + süre kontrolü kodda)"
  - "shared/src/Cache/PageCacheAdapter.php:36-39 (false ile 'yok' imleme)"
  - ".ai/.sql/CLAUDE.md §1 — 18 DB / 171 tablo (2026-10-07); GDPR/KVKK web kaynakları data-dogrulama.md W24-W26"
validation: "2026-10-12 — 12 risk; her biri etki/olasılık + azaltma + sahip + kanıt/UNKNOWN işareti; 4 risk açık sorularla (S-1/S-2/S-7/S-9) kilitli."
open_questions:
  - "KVKK md.17 silme talebi hangi iş akışıyla karşılanacak (R-04 → S-7) — karar verilmedi"
  - "Yedek RPO/RTO hedefleri yazılmadı (R-08) — K018-devops ile ortak belirlenecek"
  - "ECS tipi düşüşü (R-07) ölçülmedi — proxy nasıl davranır bilinmiyor"
group: kanit
---

# K007-data / riskler — Risk Defteri

> Format: **E** = etki (Yüksek/Orta/Düşük), **O** = olasılık, **A** = azaltma,
> **S** = sahip. Her riskin arkasında kanıt (dosya/satır veya web ID) ya da `UNKNOWN`
> işareti vardır; korkutma amaçlı uydurma iddia yoktur.

---

## R-01 · 18 DB arası dağıtık atomiklik yok — `E:Yüksek · O:Orta`

- **Tanım:** `beginTransaction/commit/rollBack` tek PDO üzerinde (`DatabaseManager.php:49-62`);
  iki veritabanını kapsayan bir yazma **iki ayrı transaction** demektir — kısmi başarı
  durumunda veri tutarsızlığı possible.
- **Kanıt:** Ö-2 (kod); ADR-100 başlığı cross-db outbox'a işaret eder (metin `⚠️ VERIFICATION REQUIRED`).
- **A:** Kısa vade: çapraz-DB yazmalarda sıralama + iş kurtarma (retry/idempotency);
  orta vade: K-07 kararı (outbox/CDC). **S:** K007 + K013-integraion (S-9).
- **Sınırlılık:** Aynı transaction içinde kalan tek-DB işlemleri bundan etkilenmez.

## R-02 · Çoklu-DB FK zayıflığı — `E:Orta · O:Orta`

- **Tanım:** `oauth_connections` FK'sı `coremusic_user.users`'ı referans alır
  (`oauth_connections_migration.php:41`) — **DB sınırını aşan referans**. InnoDB FK
  denetimi tek DB içinde çalışır; aradaki tutarlılık uygulama seviyesinde kalır.
- **Kanıt:** Ö-8. ⚠️ İki DB'nin InnoDB olduğu dosya beyanı, canlı doğrulama `⚠️ VERIFICATION REQUIRED`.
- **A:** Kritik arası-DB referanslarda uygulama-seviyesi doğrulama (repository kontrolü);
  şema revizyonunda FK'yi uygulamaya taşımak seçeneği K-SCOPE kapsamında değerlendirilir.
- **S:** Data Engineer / K007-schema.

## R-03 · `SELECT *` yasağı ihlali sorgu-metni düzeyinde — `E:Orta · O:Orta`

- **Tanım:** Yasağın denetim noktası `DatabaseManager` değil, **çağıran metin** —
  yeni repository sorguları sütun listesiz yazılabilir.
- **Kanıt:** Ö-3/Ö-5 (iyi örnek) — otomatik lint kanıtı yok.
- **A:** PR denetiminde `SELECT\s+\*` araması (statik tarama); repository şablonuna
  `SELECT_COLUMNS` sabiti zorunluluğu. **S:** code-review / QA.

## R-04 · Soft delete ≠ KVKK/GDPR silme — `E:Yüksek · O:Düşük` (talep gelirse Yüksek)

- **Tanım:** `is_deleted=1` kaydı **tabloda tutar**; GDPR md.17 / KVKK md.7 gereği
  tam silme talebinde mevcut model karşılamaz.
- **Kanıt:** Ö-7 (`AND is_deleted = 0`), Ö-8 (`is_deleted` kolonu); web W24/W25 (GDPR md.17),
  W44 (KVKK md.7).
- **A:** (a) Fiziksel silme/pseudonimleştirme iş akışı tasarımı (K-SCOPE), (b) talep
  kayıt-defteri, (c) cascade etki analizi (hangi tablolar FK ile bağlı). **S:** K007-data-quality
  + K014-security (DPIA yüzeyi), açık soru **S-7**.
- **Not:** Silme talebi **gelmediyse** risk latent'tir; olduğunda E=Yüksek'e yükselir.

## R-05 · Sayfa önbelleğinde `false` belirsizliği — `E:Düşük · O:Orta`

- **Tanım:** `remember()` `get() !== false` der (`PageCacheAdapter.php:36-39`); `CacheInterface::get(): mixed`
  `false` döndürebildiğinden, `false` cache'lenen bir değer "yok" sanılır ve her istekte
  callback çalışır (doğru sonuç döner, önbellek koruması bozulur).
- **Kanıt:** Ö-4 + `CacheInterface.php:7`. **Davranış hatası olduğu iddia edilmez** —
  `⚠️ TASARIM NOTU`.
- **A:** `has()` ile ayırma ya da sentinel (`null` + `has`) kullanımı; adapter sözleşmesine
  "yok" tanımı eklemek. **S:** K007-cache.

## R-06 · Önbellek sızıntısı: kimlik-ayrımlı anahtar ama TTL ile sınırlı savunma — `E:Orta · O:Düşük`

- **Tanım:** `page_auth_` anahtarı girişli kullanıcı sayfalarını ayrı tutar; ancak
  **çıkış (logout) sonrası** oturum anahtarının/`page_auth_` girdilerinin temizliği
  kodda gösterilemedi — TTL 600 sn'ye kadar eski içerik kalabilir.
- **Kanıt:** Ö-4 (anahtar üretimi), çıkış-temizleme kanıtı `⚠️ VERIFICATION REQUIRED`.
- **A:** Logout'ta `page_auth_*` taraması/kök-silme; kısa TTL bilinçli kabul. **S:** K007-cache + K0100-identitiy.

## R-07 · Kalıcı PDO bağlantı + `getPdo()` sızıntısı — `E:Orta · O:Düşük`

- **Tanım:** `ATTR_PERSISTENT=true` (Ö-1) havuzlanmış bağlantı demektir; beklenmedik
  disconnect sonrası yeni istekte "server has gone away" sınıfı hata possible. Ayrıca
  `getPdo()` (Ö-2) ham PDO'yu dışarı açar — katman ihlali kapısı.
- **Kanıt:** Ö-1, Ö-2.
- **A:** Yeniden bağlanma yolu (istisna → yeni bağlantı), `getPdo()` kullanımının
  envanteri (nerede çağrılıyor — `⚠️ VERIFICATION REQUIRED`). **S:** Backend Architect.

## R-08 · Yedek RPO/RTO tanımsız + sahiplik belirsiz — `E:Yüksek · O:Orta`

- **Tanım:** 3-2-1 kuralı hedef olarak yazıldı (web W41/W46) ama **kaç saatlik veri
  kaybı kabul edilir (RPO) / kaç saatte kurtarma (RTO)** sayı olarak yok; yedek
  operasyonunun sahibi de açık (K-09/S-2).
- **Kanıt:** `data-web-arastirma.md` §9; tek somut geri-öz-dönüş örneği Ö-9
  (mysqldump → CREATE+INSERT).
- **A:** RPO/RTO değerlerini K17-devops ile birlikte yazmak; tek-tablo değil **18 DB
  envanteri** bazında yedek planı. **S:** K007-backup + K17-devops.

## R-09 · Yıkıcı migration onay zinciri kırılabilir — `E:Yüksek · O:Düşük`

- **Tanım:** Protokol kodda var (Ö-9: `destructive`, `approval`, `precheck`), ancak
  `precheck` sonuçlarını **okuyan ve onayı toplayan** runner yüzeyi bilinmiyor
  (`UNKNOWN`) — zincirin bir halkası süreçte kalırsa DROP koşulsuz çalışabilir.
- **Kanıt:** Ö-9 (dönüş anahtarları), runner `⚠️ VERIFICATION REQUIRED`.
- **A:** Runner'ın `destructive=true` durumunda onay tokenizer'ını zorunlu kılması;
  CI'da DROP içeren migration'lara otomatik engel. **S:** K007-migrations + DevOps.

## R-10 · İndeks-şema sürüklenmesi (171 tablo) — `E:Orta · O:Yüksek`

- **Tanım:** Niyet beyanı indeks listeleri (Ö-7 yorum satırı 18-19) ile canlı şemanın
  birebirliği düzenli olarak **denetlenmiyor**; sorgu planı bozulabilir.
- **Kanıt:** `.ai/.sql/CLAUDE.md §1` (döküm) vs kod-yorum beyanı.
- **A:** Periyodik `EXPLAIN`/`SHOW INDEX` karşılaştırması (K007-indexing P-06 bileşeni);
  indeks değişikliklerinin yalnız migration'dan geçmesi (K-10). **S:** K007-indexing.

## R-11 · Arama sahipliği belirsizliği iki domaini kilitlemiş — `E:Orta · O:Yüksek`

- **Tanım:** Motor kararı (K-06) verilmeden hem `search/` (K007) hem K020-discovery
  planlama yapamıyor; iki taraf da "diğer sahiplenecek" varsayımıyla bekleyebilir.
- **Kanıt:** S-1/S-6; `.ai/architecture/K021-recomenataion/` ağacının varlığı (disk).
- **A:** Tek karar oturumu (motor + sahiplik birlikte). **S:** orchestrator + K020.

## R-12 · İki migration deseni standardı — `E:Düşük · O:Yüksek`

- **Tanım:** Düz `$queries` listesi (Ö-8) ve anahtarlı dizi (Ö-9) birlikte var; yeni
  migration yazarı hangisini kullanacağını bilmiyor → runner uyumsuzluğu.
- **Kanıt:** Ö-8 vs Ö-9.
- **A:** Tek şablon seçimi + `shared/AGENTS.md` §4 Zorunlu-4'teki migration şablonuna
  desen kararı eklenmesi (şablon dosyası `../.ai/.templates/infrastructure/migration-template.md`
  referansı mevcut, içeriği K007 dışı). **S:** Data Engineer.

---

## Risk özeti matrisi

| Risk | Etki | Olasılık | Kilit soru | Sahip |
|---|---|---|---|---|
| R-01 dağıtık atomiklik | Yüksek | Orta | S-9 | K007 + K013 |
| R-02 arası-DB FK | Orta | Orta | — | K007-schema |
| R-03 SELECT * ihlali | Orta | Orta | — | code-review |
| R-04 KVKK silme | Yüksek | Düşük | S-7 | K007 + K014 |
| R-05 false-belirsizliği | Düşük | Orta | — | K007-cache |
| R-06 logout önbellek | Orta | Düşük | — | K007 + K010 |
| R-07 kalıcı bağlantı | Orta | Düşük | — | Backend |
| R-08 RPO/RTO | Yüksek | Orta | S-2 | K007 + K018 |
| R-09 onay zinciri | Yüksek | Düşük | — | K007 + DevOps |
| R-10 indeks sürüklenmesi | Orta | Yüksek | — | K007-indexing |
| R-11 arama sahipliği | Orta | Yüksek | S-1/S-6 | orchestrator |
| R-12 migration deseni | Düşük | Yüksek | — | Data Engineer |

**Takip kuralı:** Bir risk kapanırsa ilgili karar (K-xx) ve `data-dogrulama.md` satırı
aynı değişiklikte güncellenir; açık soru (S-x) kapanışı tek başına yeterli değildir.
