<?php declare(strict_types=1);

namespace CoreMusic\Home\Test\Unit\Session;

use CoreMusic\Home\Session\HomeSessionManager;
use CoreMusic\Security\SessionKeys;
use CoreMusic\Session\SessionBootstrapper;
use PHPUnit\Framework\TestCase;

/**
 * Player medya kalıcılığı — HomeSessionManager media accessors.
 *
 * footer.php her tam sayfa yükünde cookie'yi (mediaType / isPlaying)
 * allowlist'ten geçirip bu setter'larla $_SESSION'a mirror'lar; boot JSON'u
 * (window.CM_PLAYER_BOOT) ise getter'lardan üretilir.
 *
 * Kurallar:
 *  - set* allowlist dışı değerde YAZMAZ (mevcut değer korunur)
 *  - get* hiç yazılmamışsa default döner (audio / stop)
 *  - arayüz (ISessionManager) DEĞİŞMEDİ — media metotları sınıf özeli
 */
final class HomeSessionManagerMediaTest extends TestCase
{
    private HomeSessionManager $session;

    protected function setUp(): void
    {
        $_SESSION = [];
        SessionBootstrapper::ensureStarted();
        $this->session = new HomeSessionManager();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        SessionBootstrapper::writeClose();
    }

    public function testSetGetRoundtrip_mediaType(): void
    {
        foreach (['audio', 'video'] as $type) {
            $this->session->setMediaType($type);

            $this->assertSame($type, $this->session->getMediaType());
            $this->assertSame($type, $_SESSION[SessionKeys::GET_MEDIA_TYPE]);
        }
    }

    public function testSetGetRoundtrip_mediaStatus(): void
    {
        foreach (['play', 'pause', 'stop'] as $status) {
            $this->session->setMediaStatus($status);

            $this->assertSame($status, $this->session->getMediaStatus());
            $this->assertSame($status, $_SESSION[SessionKeys::GET_MEDIA_STATUS]);
        }
    }

    public function testInvalidMediaType_isNotWrittenAndCurrentValueKept(): void
    {
        $this->session->setMediaType('video');

        $this->session->setMediaType('bogus');

        $this->assertSame('video', $this->session->getMediaType(), 'Allowlist ihlali mevcut değeri bozmamalı');
        $this->assertSame('video', $_SESSION[SessionKeys::GET_MEDIA_TYPE]);
    }

    public function testInvalidMediaStatus_isNotWrittenAndCurrentValueKept(): void
    {
        $this->session->setMediaStatus('play');

        $this->session->setMediaStatus('bogus');

        $this->assertSame('play', $this->session->getMediaStatus(), 'Allowlist ihlali mevcut değeri bozmamalı');
        $this->assertSame('play', $_SESSION[SessionKeys::GET_MEDIA_STATUS]);
    }

    public function testDefaults_whenKeysNeverWritten(): void
    {
        $this->assertArrayNotHasKey(SessionKeys::GET_MEDIA_TYPE, $_SESSION);
        $this->assertArrayNotHasKey(SessionKeys::GET_MEDIA_STATUS, $_SESSION);

        $this->assertSame('audio', $this->session->getMediaType());
        $this->assertSame('stop', $this->session->getMediaStatus());
    }
}
