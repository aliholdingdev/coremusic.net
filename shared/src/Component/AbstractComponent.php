<?php

declare(strict_types=1);

namespace CoreMusic\Component;

/**
 * AbstractComponent — Template method pattern ile base component.
 *
 * Lifecycle: init → renderTemplate → renderChildren → postRender
 * Children: compose pattern ile iç içe component desteği
 * Security: h(), attr(), json() ile XSS koruması
 *
 * @package CoreMusic\Component
 */
abstract class AbstractComponent implements ComponentInterface
{
    /**
     * Bileşen durumu — tüm veri bu dizide tutulur.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Çocuk bileşenler — isim => instance mapping.
     *
     * @var array<string, ComponentInterface>
     */
    protected array $children = [];

    /**
     * Özel template fonksiyonları — template engine'e kaydedilen helper'lar.
     *
     * @var array<string, callable>
     */
    protected array $customFunctions = [];

    /**
     * @param array<string, mixed> $data Bileşen başlangıç verisi
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /* ═══════════════════════════════════════════════════════════
     * TEMPLATE METHOD (final — alt sınıflar değiştirmez)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Render zincirini çalıştırır: init → renderTemplate → renderChildren → postRender
     *
     * @return string Tam HTML çıktısı
     */
    final public function render(): string
    {
        $this->init();

        $html = $this->renderTemplate();
        $html .= $this->renderChildren();
        $html .= $this->postRender();

        return $html;
    }

    /**
     * Başlangıç işlemleri — alt sınıflar bu metodu override edebilir.
     * Render'dan önce bir kez çağrılır.
     */
    protected function init(): void
    {
        // Varsayılan: boş — alt sınıflar doldurur
    }

    /**
     * Ana template'i render eder — her alt sınıf bunu uygulamak ZORUNDADIR.
     *
     * @return string HTML çıktısı
     */
    abstract protected function renderTemplate(): string;

    /**
     * Render sonrası ek HTML — opsiyonel, varsayılan boş.
     *
     * @return string Ek HTML veya boş string
     */
    protected function postRender(): string
    {
        return '';
    }

    /* ═══════════════════════════════════════════════════════════
     * CHILDREN MANAGEMENT (compose pattern)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Çocuk bileşen ekler.
     *
     * @param string               $name  Çocuk adı (unique key)
     * @param ComponentInterface   $child Component instance
     */
    public function addChild(string $name, ComponentInterface $child): void
    {
        $this->children[$name] = $child;
    }

    /**
     * Çocuk bileşen kaldırır.
     *
     * @param string $name Kaldırılacak çocuk adı
     */
    public function removeChild(string $name): void
    {
        unset($this->children[$name]);
    }

    /**
     * Tüm çocuk component'leri döndürür.
     *
     * @return array<string, ComponentInterface>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Tüm çocukları sırayla render eder.
     * Template'den `<?php echo $this->renderChildren(); ?>` ile çağrılır.
     *
     * @return string Tüm çocukların HTML çıktısı
     */
    final protected function renderChildren(): string
    {
        $html = '';
        foreach ($this->children as $child) {
            $html .= $child->render();
        }
        return $html;
    }

    /**
     * Tek bir çocuğu isimle render eder.
     *
     * @param string $name Çocuk adı
     * @return string HTML çıktısı (çocuk yoksa boş string)
     */
    final protected function renderChild(string $name): string
    {
        return $this->children[$name]?->render() ?? '';
    }

    /* ═══════════════════════════════════════════════════════════
     * DATA MANAGEMENT
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Bileşen veri dizisini döndürür (ComponentInterface).
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Tek alan günceller.
     *
     * @param string $key   Alan adı
     * @param mixed  $value Yeni değer
     */
    public function setData(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
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
        return $this->data[$key] ?? $default;
    }

    /**
     * Bağımlılık listesi — DI container için (ComponentInterface).
     *
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [];
    }

    /* ═══════════════════════════════════════════════════════════
     * SECURITY HELPERS (final — asla override edilmez)
     * ═══════════════════════════════════════════════════════════ */

    /**
     * HTML escape — XSS koruması. Tüm пользователь girdisi için zorunlu.
     *
     * @param string $value Escape edilecek değer
     * @return string Güvenli HTML string
     */
    final protected function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Attribute escape — HTML attribute değerleri için.
     *
     * @param string $value Escape edilecek attribute değeri
     * @return string Güvenli attribute string
     */
    final protected function attr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * JSON escape — data attributes için güvenli JSON.
     *
     * @param mixed $data JSON'a dönüştürülecek veri
     * @return string Güvenli JSON string (HTML attribute içinde kullanılır)
     */
    final protected function json(mixed $data): string
    {
        return htmlspecialchars(
            json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );
    }

    /* ═══════════════════════════════════════════════════════════
     * ASSET HELPERS
     * ═══════════════════════════════════════════════════════════ */

    /**
     * ASSETS_URL önekli varlık adresi üretir.
     *
     * @param string $path Assets yolu (ör: '/Css/main.css')
     * @return string Tam URL
     */
    final protected function asset(string $path): string
    {
        $base = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }

    /* ═══════════════════════════════════════════════════════════
     * TEMPLATE FUNCTIONS
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Özel template fonksiyonu kaydeder (formatDate, formatDuration vb.).
     *
     * @param string   $name Fonksiyon adı
     * @param callable $fn   Fonksiyon referansı
     */
    protected function registerFunction(string $name, callable $fn): void
    {
        $this->customFunctions[$name] = $fn;
    }

    /**
     * Kayıtlı template fonksiyonunu çağırır.
     *
     * @param string $name Fonksiyon adı
     * @param mixed  ...$args Argümanlar
     * @return mixed Fonksiyon dönüş değeri
     *
     * @throws \RuntimeException Fonksiyon kayıtlı değilse
     */
    final protected function callFunction(string $name, mixed ...$args): mixed
    {
        if (!isset($this->customFunctions[$name])) {
            throw new \RuntimeException("Template function '{$name}' not registered");
        }
        return ($this->customFunctions[$name])(...$args);
    }
}
