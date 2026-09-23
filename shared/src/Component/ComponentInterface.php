<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * ComponentInterface — Evrensel component sözleşmesi.
 *
 * Tüm component'ler bu arayüzü uygulamak zorundadır.
 * Registry, Renderer ve DataBinder bu sözleşme üzerinden çalışır.
 *
 * @package CoreMusic\Component
 */
interface ComponentInterface
{
    /**
     * Bileşen benzersiz anahtarı.
     * Registry'de isim, template lookup'ta key olarak kullanılır.
     *
     * @return string BEM block adı (ör: 'cm-card', 'cm-modal')
     */
    public function key(): string;

    /**
     * Bileşen HTML çıktısını üretir.
     * Template method pattern: init → renderTemplate → renderChildren → postRender
     *
     * @return string Tam HTML çıktısı
     */
    public function render(): string;

    /**
     * Bileşen veri dizisini döndürür.
     * Test edilebilirlik ve debug amaçlıdır.
     *
     * @return array<string, mixed> Bileşen durumu
     */
    public function getData(): array;

    /**
     * Bileşen bağımlılıklarını döndürür.
     * DI container entegrasyonu için opsiyonel bilgi.
     *
     * @return array<int, string> Bağımlılık class adları
     */
    public function getDependencies(): array;
}
