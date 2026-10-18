<?php declare(strict_types=1);

/**
 * Karakterizasyon testi fixture'ı — production partial DEĞİL.
 *
 * Partialın ortasında istisna fırlatma senaryosu: render() catch bloğunda
 * ob_end_clean ile buffer'ı temizlemeli ve istisnayı yeniden fırlatmalı.
 */
echo 'PARTIAL-LEAK';
throw new \RuntimeException('partial failed');
