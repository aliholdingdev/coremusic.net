<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * ComponentRenderer — Facade pattern ile component render.
 *
 * Registry + TemplateEngine'i birleştirir, basit render API'ı sunar.
 * Tek component, çoklu component ve parent-child kompozisyonu destekler.
 *
 * @package CoreMusic\Component
 */
final class ComponentRenderer
{
    private ComponentRegistry $registry;
    private TemplateEngine $engine;

    public function __construct(
        ?ComponentRegistry $registry = null,
        ?TemplateEngine $engine = null,
    ) {
        $this->registry = $registry ?? ComponentRegistry::getInstance();
        $this->engine = $engine ?? new TemplateEngine();
    }

    /**
     * Tek bir component'ı registry'den bulup render eder.
     *
     * @param string                $name Registry anahtarı (ör: 'cm-card')
     * @param array<string, mixed> $data Constructor argümanları
     *
     * @return string Render edilmiş HTML
     *
     * @throws \InvalidArgumentException Component bulunamazsa
     */
    public function render(string $name, array $data = []): string
    {
        $component = $this->registry->create($name, $data);
        return $component->render();
    }

    /**
     * Component'ı doğrudan çıktıya yazar (echo).
     *
     * @param string                $name Registry anahtarı
     * @param array<string, mixed> $data Constructor argümanları
     */
    public function display(string $name, array $data = []): void
    {
        echo $this->render($name, $data);
    }

    /**
     * Birden fazla component'ı sırayla render eder.
     *
     * @param array<array{name: string, data?: array<string, mixed>}> $components
     *
     * @return string Tüm component'lerin birleştirilmiş HTML'i
     */
    public function renderMany(array $components): string
    {
        $html = '';
        foreach ($components as $item) {
            $html .= $this->render($item['name'], $item['data'] ?? []);
        }
        return $html;
    }

    /**
     * Parent component'a child'lar ekleyip render eder.
     *
     * @param string                $parentName  Parent registry anahtarı
     * @param array<string, mixed> $parentData  Parent constructor argümanları
     * @param array<string, array<string, mixed>> $children name => data mapping
     *
     * @return string Parent + children render'ı
     */
    public function renderWithChildren(
        string $parentName,
        array $parentData,
        array $children,
    ): string {
        $parent = $this->registry->create($parentName, $parentData);

        if ($parent instanceof AbstractComponent) {
            foreach ($children as $childName => $childData) {
                $child = $this->registry->create($childName, $childData);
                $parent->addChild($childName, $child);
            }
        }

        return $parent->render();
    }

    /**
     * Template engine'e erişim — global fonksiyon kaydı için.
     */
    public function getEngine(): TemplateEngine
    {
        return $this->engine;
    }

    /**
     * Registry'ye erişim — component kaydı için.
     */
    public function getRegistry(): ComponentRegistry
    {
        return $this->registry;
    }
}
