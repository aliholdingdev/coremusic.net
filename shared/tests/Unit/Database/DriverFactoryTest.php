<?php declare(strict_types=1);

namespace CoreMusic\Test\Unit\Database;

use CoreMusic\Database\DatabaseRegistry;
use CoreMusic\Database\MongoDbManager;
use CoreMusic\Database\SQLiteDatabaseManager;
use CoreMusic\Database\SqlsrvDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseManager;
use PHPUnit\Framework\TestCase;

/**
 * Driver factory tests — verifies that DatabaseRegistry correctly instantiates
 * the appropriate driver manager based on provider type.
 *
 * These tests do NOT require live database connections; they only verify
 * that the correct class is instantiated and driver detection works.
 */
final class DriverFactoryTest extends TestCase
{
    private DatabaseRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new DatabaseRegistry();
    }

    public function testRegisterMySqlCreatesDatabaseManager(): void
    {
        $this->registry->registerMySql('test_mysql', 'localhost', 'testdb', 'user', 'pass', 3306, 'utf8mb4');
        $manager = $this->registry->get('test_mysql');

        $this->assertInstanceOf(\CoreMusic\Database\DatabaseManager::class, $manager);
        $this->assertInstanceOf(IDatabaseManager::class, $manager);
    }

    public function testRegisterProviderMysqlCreatesDatabaseManager(): void
    {
        $this->registry->registerProvider('test_mysql2', 'mysql', 'localhost', 'testdb', 'user', 'pass', 3306, 'utf8mb4');
        $manager = $this->registry->get('test_mysql2');

        $this->assertInstanceOf(\CoreMusic\Database\DatabaseManager::class, $manager);
    }

    public function testRegisterProviderMariadbCreatesDatabaseManager(): void
    {
        $this->registry->registerProvider('test_mariadb', 'mariadb', 'localhost', 'testdb', 'user', 'pass', 3306, 'utf8mb4');
        $manager = $this->registry->get('test_mariadb');

        $this->assertInstanceOf(\CoreMusic\Database\DatabaseManager::class, $manager);
    }

    public function testRegisterProviderSqliteCreatesSqliteDatabaseManager(): void
    {
        // SQLite uses host as file path; other params ignored
        // registerProvider(key, providerType, host, dbName, user, password, port, charset)
        $this->registry->registerProvider('test_sqlite', 'sqlite', ':memory:', '', '', '', 0, '');
        $manager = $this->registry->get('test_sqlite');

        $this->assertInstanceOf(SQLiteDatabaseManager::class, $manager);
        $this->assertInstanceOf(IDatabaseManager::class, $manager);
    }

    public function testRegisterProviderSqliteWithFilePath(): void
    {
        $path = sys_get_temp_dir() . '/test_' . uniqid() . '.sqlite';
        $this->registry->registerProvider('test_sqlite_file', 'sqlite', $path, '', '', '', 0, '');
        $manager = $this->registry->get('test_sqlite_file');

        $this->assertInstanceOf(SQLiteDatabaseManager::class, $manager);
        // Clean up
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function testRegisterProviderMssqlCreatesSqlsrvDatabaseManager(): void
    {
        if (!\extension_loaded('pdo_sqlsrv')) {
            $this->markTestSkipped('pdo_sqlsrv not loaded — SqlsrvDatabaseManager instantiation skipped');
        }

        $this->registry->registerProvider('test_mssql', 'mssql', 'localhost', 'testdb', 'sa', 'password', 1433, 'utf8');
        $manager = $this->registry->get('test_mssql');

        $this->assertInstanceOf(SqlsrvDatabaseManager::class, $manager);
        $this->assertInstanceOf(IDatabaseManager::class, $manager);
    }

    public function testRegisterProviderMssqlThrowsOnMissingExtension(): void
    {
        if (\extension_loaded('pdo_sqlsrv')) {
            $this->markTestSkipped('pdo_sqlsrv is loaded — cannot test missing extension behavior');
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('pdo_sqlsrv not available');

        $this->registry->registerProvider('test_mssql_fail', 'mssql', 'localhost', 'testdb', 'sa', 'password', 1433, 'utf8');
    }

    public function testRegisterProviderMongoDBCreatesMongoDbManager(): void
    {
        if (!\extension_loaded('mongodb')) {
            $this->markTestSkipped('mongodb extension not loaded — MongoDbManager instantiation skipped');
        }

        $this->registry->registerProvider('test_mongo', 'mongodb', 'localhost', 'testdb', '', '', 27017, 'utf8');
        $manager = $this->registry->get('test_mongo');

        $this->assertInstanceOf(MongoDbManager::class, $manager);
        $this->assertInstanceOf(IDatabaseManager::class, $manager);
    }

    public function testRegisterProviderMongoDBThrowsOnMissingExtension(): void
    {
        if (\extension_loaded('mongodb')) {
            $this->markTestSkipped('mongodb extension is loaded — cannot test missing extension behavior');
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('mongodb not available');

        $this->registry->registerProvider('test_mongo_fail', 'mongodb', 'localhost', 'testdb', '', '', 27017, 'utf8');
    }

    public function testRegisterProviderUnsupportedThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported provider type: postgresql');

        $this->registry->registerProvider('test_pg', 'postgresql', 'localhost', 'testdb', 'user', 'pass', 5432, 'utf8');
    }

    public function testHasReturnsTrueAfterRegistration(): void
    {
        $this->assertFalse($this->registry->has('test_key'));
        $this->registry->registerMySql('test_key', 'localhost', 'testdb', 'user', 'pass');
        $this->assertTrue($this->registry->has('test_key'));
    }

    public function testGetThrowsOnUnregisteredKey(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Database 'unregistered' is not registered.");

        $this->registry->get('unregistered');
    }

    public function testSameKeyReturnsSameInstance(): void
    {
        $this->registry->registerMySql('singleton_test', 'localhost', 'testdb', 'user', 'pass');
        $manager1 = $this->registry->get('singleton_test');
        $manager2 = $this->registry->get('singleton_test');

        $this->assertSame($manager1, $manager2, 'Registry should return same instance for same key');
    }

    public function testSqliteInMemoryExecuteWorks(): void
    {
        $this->registry->registerProvider('sqlite_mem', 'sqlite', ':memory:', '', '', '', 0, '');
        $manager = $this->registry->get('sqlite_mem');

        // Create a test table
        $manager->write('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $manager->write('INSERT INTO test (name) VALUES (?)', ['test_value']);

        $results = $manager->execute('SELECT * FROM test WHERE name = ?', ['test_value']);

        $this->assertCount(1, $results);
        $this->assertSame('test_value', $results[0]['name']);
    }

    public function testSqliteLastInsertIdWorks(): void
    {
        $this->registry->registerProvider('sqlite_lid', 'sqlite', ':memory:', '', '', '', 0, '');
        $manager = $this->registry->get('sqlite_lid');

        $manager->write('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $manager->write('INSERT INTO test (name) VALUES (?)', ['value1']);
        $id1 = $manager->lastInsertId();
        $manager->write('INSERT INTO test (name) VALUES (?)', ['value2']);
        $id2 = $manager->lastInsertId();

        $this->assertNotEmpty($id1);
        $this->assertNotEmpty($id2);
        $this->assertNotSame($id1, $id2);
    }

    public function testSqliteRunInTransactionCommits(): void
    {
        $this->registry->registerProvider('sqlite_tx', 'sqlite', ':memory:', '', '', '', 0, '');
        $manager = $this->registry->get('sqlite_tx');

        $manager->write('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');

        $manager->runInTransaction(function ($pdo) {
            $pdo->prepare('INSERT INTO test (name) VALUES (?)')->execute(['committed']);
        });

        $results = $manager->execute('SELECT * FROM test WHERE name = ?', ['committed']);
        $this->assertCount(1, $results);
    }

    public function testSqliteRunInTransactionRollsBackOnException(): void
    {
        $this->registry->registerProvider('sqlite_rb', 'sqlite', ':memory:', '', '', '', 0, '');
        $manager = $this->registry->get('sqlite_rb');

        $manager->write('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');

        $this->expectException(\RuntimeException::class);

        $manager->runInTransaction(function ($pdo) {
            $pdo->prepare('INSERT INTO test (name) VALUES (?)')->execute(['will_rollback']);
            throw new \RuntimeException('Intentional rollback');
        });

        $results = $manager->execute('SELECT * FROM test WHERE name = ?', ['will_rollback']);
        $this->assertCount(0, $results, 'Transaction should have rolled back');
    }
}