---
node_id: "K007-data-quality-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
evidence:
  - "shared/tests/Repository/ApiKeyRepositoryTest.php:6-9,23-30 — fixture (2026-10-12 okundu)"
validation: "2026-10-15 — gerçek test kodu"
open_questions: []
group: kod
---

# data-quality — Örnekler (K007)

## §1 Repo gerçeği: in-memory fixture

Dosya: shared/tests/Repository/ApiKeyRepositoryTest.php (satır 6-9, 23-30, özet):

    // fixture: in-memory SQLite + şema kurulumu (repository sözleşmesi birebir)
    private function makeRepository(): ApiKeyRepository
    {
        $registry = new DatabaseRegistry();
        $registry->registerSqlite('auth', ':memory:');
        return new ApiKeyRepository($registry);
    }

    #[Test] public function create_return_array_and_hash(): void
    {
        $id = $this->repo->create([...]);
        $this->assertIsString($id);
        // ham anahtar DB'ye gitmez -> hash doğrulaması
    }

> Satırlar özetlenmiştir; kesin okuma için dosyaya bakınız (2026-10-12 kanıtı).

## §2 Örnek okuma

| Ders | Açıklama |
|------|----------|
| Gerçek DB yerine in-memory | test hızlı + izole + kalıcı kirletme yok |
| Şema fixture'ı birebir | test, canlı MySQL ile aynı constraint'leri görür |
| Hash-only doğrulama | kalite testi güvenlik kuralını da kilitler |

---

**Authority:** Bayram Ali / Vault Steward