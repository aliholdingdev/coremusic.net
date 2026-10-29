<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Contracts\Database\INoSqlDatabaseManager;
use CoreMusic\Database\DatabaseException;

/**
 * MongoDbManager — MongoDB (mongodb extension) implementation of INoSqlDatabaseManager.
 *
 * Driver: mongodb (modern MongoDB PHP driver, NOT legacy 'mongo' extension).
 * DSN: mongodb://{host}:{port}/{dbname} (buildDsn — parola mesaja ASLA taşınmaz).
 * Requires: extension_loaded('mongodb') — yoksa constructor RuntimeException.
 *
 * Fail-closed politika (2026-10-29):
 *  - execute(): bozuk/boş JSON filtre veya tanımsız koleksiyon → DatabaseException
 *    ('default' koleksiyon sihri ve "boş filtre = tüm koleksiyon" davranışı YASAKTIR).
 *  - write(): boş/bozuk operasyon listesi, tanımsız koleksiyon, bilinmeyen
 *    operasyon tipi veya sürücü hatası → DatabaseException (sesiz false DÖNDÜRMEZ).
 *  - lastInsertId() '' döner (ObjectId store'da); supportsTransactions() false döner.
 *
 * Concurrency: MongoDB driver connection pooling (dahili).
 * Fork safety: Manager fork-safe DEĞİL; child süreç YENİ manager kurmalı.
 *
 * NOT (Adım 03): execute()/write() mevcut (repository sözleşmesi) — SQL katmanı
 * repository'dir; bağlantı katmanı yeni SQL API üretmez.
 */
final class MongoDbManager implements INoSqlDatabaseManager
{
    private ?\MongoDB\Driver\Manager $manager = null;
    private string $dbName;

    public function __construct(
        private readonly string $host = 'localhost',
        string $dbName = 'coremusic',
        private readonly string $user = '',
        private readonly string $password = '',
        private readonly int $port = 27017,
    ) {
        if (!\extension_loaded('mongodb')) {
            throw new \RuntimeException(
                'MongoDB driver mongodb not available: install it (composer require mongodb/mongodb or enable the WAMP extension).'
            );
        }

        $this->dbName = $dbName;
    }

    /**
     * Get the underlying MongoDB\Driver\Manager.
     * This is not a PDO object but provides the connection.
     */
    public function getManager(): \MongoDB\Driver\Manager
    {
        if ($this->manager === null) {
            $this->manager = new \MongoDB\Driver\Manager($this->buildDsn());
        }

        return $this->manager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $query, array $params = []): array
    {
        $filter = json_decode($query, true);

        // Fail-closed: "boş filtre" her satırı döndürürdü — open-by-default yasaK.
        if (!is_array($filter) || $filter === []) {
            throw DatabaseException::operationFailed('MongoDB', 'okuma (sorgu geçersiz)');
        }

        $options    = $params;
        $collection = $options['collection'] ?? null;
        unset($options['collection']);

        if (!is_string($collection) || $collection === '') {
            throw DatabaseException::operationFailed('MongoDB', 'okuma (koleksiyon tanımsız)');
        }

        $queryObj = new \MongoDB\Driver\Query($filter, $options);
        $cursor   = $this->getManager()->executeQuery("{$this->dbName}.{$collection}", $queryObj);

        $results = [];
        foreach ($cursor as $document) {
            $results[] = $document;
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $query, array $params = []): bool
    {
        $operations = json_decode($query, true);

        if (!is_array($operations) || $operations === []) {
            throw DatabaseException::operationFailed('MongoDB', 'yazma (operasyon listesi geçersiz)');
        }

        $collection = $params['collection'] ?? null;
        if (!is_string($collection) || $collection === '') {
            throw DatabaseException::operationFailed('MongoDB', 'yazma (koleksiyon tanımsız)');
        }

        $bulk = new \MongoDB\Driver\BulkWrite();

        foreach ($operations as $op) {
            if (!is_array($op)) {
                throw DatabaseException::operationFailed('MongoDB', 'yazma (operasyon geçersiz)');
            }

            $type = $op['type'] ?? 'insert';
            switch ($type) {
                case 'insert':
                    $bulk->insert($op['document'] ?? []);
                    break;
                case 'update':
                    $bulk->update(
                        $op['filter'] ?? [],
                        ['$set' => $op['document'] ?? []],
                        ($op['options'] ?? []) + ['multi' => false, 'upsert' => false],
                    );
                    break;
                case 'delete':
                    $bulk->delete($op['filter'] ?? [], ($op['options'] ?? []) + ['limit' => 1]);
                    break;
                default:
                    // Sessiz ignore = boş bulk + false-success — fail-closed yasak.
                    throw DatabaseException::operationFailed('MongoDB', 'yazma (bilinmeyen operasyon tipi)');
            }
        }

        try {
            $this->getManager()->executeBulkWrite("{$this->dbName}.{$collection}", $bulk);

            return true;
        } catch (\Throwable $e) {
            // Sürücü Detayları mesajda taşınmaz (DSN sızabilir) — $previous'ta.
            throw DatabaseException::operationFailed('MongoDB', 'yazma', $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(): string
    {
        // MongoDB generates ObjectId on insert; we can't easily return it from write()
        // This is a limitation of the INoSqlDatabaseManager interface for MongoDB
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function supportsTransactions(): bool
    {
        // MongoDB transactions require a session; not supported in this simple wrapper
        return false;
    }

    private function buildDsn(): string
    {
        $credentials = '';
        if ($this->user !== '' && $this->password !== '') {
            $credentials = rawurlencode($this->user) . ':' . rawurlencode($this->password) . '@';
        }

        return sprintf(
            'mongodb://%s%s:%d/%s',
            $credentials,
            $this->host,
            $this->port,
            $this->dbName,
        );
    }
}