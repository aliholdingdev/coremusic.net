<?php declare(strict_types=1);

/**
 * Karakterizasyon testi fixture'ı — production partial DEĞİL.
 *
 * home AbstractComponent::render() bu dosyayı ob_start/ob_get_clean arasında
 * `require` eder; $this = bileşen instance (variant public readonly).
 */
echo 'STUB[' . $this->variant->name . ']';
