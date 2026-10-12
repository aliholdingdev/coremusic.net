<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * ComponentRegistry — Singleton registry + lazy instantiation.
 *
 * Component class'larını isimle kaydeder, instance üretir.
 * Registry pattern: name → class-string → instance (cached).
 *
 * @package CoreMusic\Component
 */
final class ComponentRegistry
{
    private static ?self $instance = null;

    /**
     * Kayıtlı component class'ları — name => class-string mapping.
     *
     * @var array<string, class-string<ComponentInterface>>
     */
    private array $components = [];

    /**
     * Oluşturulmuş instance cache — name => instance.
     * Data yoksa singleton behavior, data varsa her zaman yeni instance.
     *
     * @var array<string, ComponentInterface>
     */
    private array $instances = [];

    private function __construct() {}

    /**
     * Singleton instance döndürür.
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Singleton'ı sıfırlar — testler için.
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Component sınıfı register eder.
     *
     * @param string                      $name      Registry anahtarı (BEM block adı)
     * @param class-string<ComponentInterface> $className Component class adı
     *
     * @throws \InvalidArgumentException Class ComponentInterface implement etmiyorsa
     */
    public function register(string $name, string $className): void
    {
        if (!is_subclass_of($className, ComponentInterface::class)) {
            throw new \InvalidArgumentException(
                "'{$className}' must implement " . ComponentInterface::class
            );
        }

        $this->components[$name] = $className;
        unset($this->instances[$name]); // cache invalidation
    }

    /**
     * Toplu kayıt — birden fazla component'i tek seferde kaydeder.
     *
     * @param array<string, class-string<ComponentInterface>> $map name => class mapping
     */
    public function registerMany(array $map): void
    {
        foreach ($map as $name => $className) {
            $this->register($name, $className);
        }
    }

    /**
     * Component kayıtlı mı kontrol eder.
     *
     * @param string $name Registry anahtarı
     */
    public function has(string $name): bool
    {
        return isset($this->components[$name]);
    }

    /**
     * Component instance üretir.
     *
     * - Data yoksa: cache'den döner (singleton behavior)
     * - Data varsa: her zaman yeni instance üretir
     *
     * @param string $name Registry anahtarı
     * @param array<string, mixed> $data Constructor argümanları
     *
     * @return ComponentInterface Component instance
     *
     * @throws \InvalidArgumentException Kayıtlı değilse
     */
    public function create(string $name, array $data = []): ComponentInterface
    {
        if (!isset($this->components[$name])) {
            throw new \InvalidArgumentException("Unknown component: '{$name}'");
        }

        $className = $this->components[$name];

        // Data varsa her zaman yeni instance
        if (!empty($data)) {
            return new $className($data);
        }

        // Data yoksa cache'den dön
        return $this->instances[$name] ??= new $className();
    }

    /**
     * Kayıtlı tüm component anahtarlarını döndürür.
     *
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->components);
    }

    /**
     * Kayıtlı tüm component'leri listeler (name => className).
     *
     * @return array<string, class-string<ComponentInterface>>
     */
    public function all(): array
    {
        return $this->components;
    }

    /**
     * Tüm cache'lenmiş instance'ları temizler.
     */
    public function clearCache(): void
    {
        $this->instances = [];
    }
}
