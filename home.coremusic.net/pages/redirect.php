<?php declare(strict_types=1);
/**
 * CoreMusic Home — Redirect Page
 *
 * SPA Router redirect handler.
 * meta['redirect_to'] URL'ine JavaScript redirect yapar.
 *
 * @var array $meta Route meta verileri
 * @var string $csrfToken CSRF token
 */

$redirectUrl = $meta['redirect_to'] ?? '/';
$redirectUrlEsc = htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8');
$cspNonce = $_SESSION['csp_nonce'] ?? '';
?>
<noscript>
    <meta http-equiv="refresh" content="0;url=<?=$redirectUrlEsc?>">
</noscript>
<script nonce="<?=$cspNonce?>">
window.location.href = <?=$redirectUrlEsc?>;
</script>
<p>Yönlendiriliyorsunuz... <a href="<?=$redirectUrlEsc?>">Buraya tıklayın</a></p>
