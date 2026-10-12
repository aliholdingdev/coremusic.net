<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * DataBinder — Shallow diff state management.
 *
 * Component durumunu yönetir, değişiklikleri takip eder.
 * Bind() ile component data'sını günceller.
 *
 * @package CoreMusic\Component
 */
final class DataBinder
{
    /**
     * Mevcut durum.
     *
     * @var array<string, mixed>
     */
    private array $state = [];

    /**
     * Başlangıç durumu — diff karşılaştırması için referans.
     *
     * @var array<string, mixed>
     */
    private array $initialState = [];

    /**
     * @param array<string, mixed> $initialState Başlangıç durumu
     */
    public function __construct(array $initialState = [])
    {
        $this->state = $initialState;
        $this->initialState = $initialState;
    }

    /**
     * Tek alan günceller.
     *
     * @param string $key   Alan adı
     * @param mixed  $value Yeni değer
     */
    public function set(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }

    /**
     * Toplu güncelleme — mevcut state üzerine merge eder.
     *
     * @param array<string, mixed> $values Güncellenecek alanlar
     */
    public function update(array $values): void
    {
        $this->state = array_merge($this->state, $values);
    }

    /**
     * Tek alan okur, yoksa varsayılan döner.
     *
     * @param string $key     Alan adı
     * @param mixed  $default Varsayılan değer
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * Tüm durumu döndürür.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->state;
    }

    /**
     * Shallow diff — başlangıç durumundan bu yana değişen alanları döndürür.
     *
     * @return array<string, array{old: mixed, new: mixed}> Değişen alanlar
     */
    public function diff(): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(
            array_keys($this->initialState),
            array_keys($this->state),
        ));

        foreach ($allKeys as $key) {
            $old = $this->initialState[$key] ?? null;
            $new = $this->state[$key] ?? null;
            if ($old !== $new) {
                $changes[$key] = ['old' => $old, 'new' => $new];
            }
        }

        return $changes;
    }

    /**
     * Değişiklik olup olmadığını kontrol eder.
     */
    public function isDirty(): bool
    {
        return !empty($this->diff());
    }

    /**
     * Başlangıç durumunu mevcut duruma sıfırlar (after save).
     */
    public function reset(): void
    {
        $this->initialState = $this->state;
    }

    /**
     * Component'e bind eder — component data'sını günceller.
     *
     * @param ComponentInterface $component Hedef component
     */
    public function bind(ComponentInterface $component): void
    {
        if ($component instanceof AbstractComponent) {
            foreach ($this->state as $key => $value) {
                $component->setData($key, $value);
            }
        }
    }
}
