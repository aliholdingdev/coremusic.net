<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Controller\HistoryController;
use CoreMusic\Api\Controller\UserHeaderController;
use CoreMusic\Api\Repository\IHistoryRepository;
use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\INotificationRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;
use CoreMusic\Api\Service\HistoryService;
use CoreMusic\Api\Service\UserHeaderService;
use PHPUnit\Framework\TestCase;

final class HistoryAndStreamTest extends TestCase
{
    public function testHistoryControllerUnauthorizedWithoutSession(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $response = $controller->handle(['action' => 'recent'], []);
        $this->assertArrayHasKey('error', $response);
        $this->assertSame('UNAUTHORIZED', $response['error']['code']);
    }

    public function testHistoryControllerRecentCallsService(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findRecentTracks')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 10)
            ->willReturn([
                [
                    'music_hex' => '018f3a5b6c7d8e9f0123456789abcdef',
                    'title' => 'Test Track',
                    'artist_name' => 'Test Artist',
                ]
            ]);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $request = [
            '_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']
        ];

        $response = $controller->handle(['action' => 'recent'], $request);
        $this->assertTrue($response['success']);
        $this->assertCount(1, $response['data']['items']);
        $this->assertSame('Test Track', $response['data']['items'][0]['title']);
    }

    public function testUserHeaderControllerAnonymousReturnsDefaults(): void
    {
        $userRepo = $this->createMock(IUserProfileRepository::class);
        $playlistRepo = $this->createMock(IPlaylistRepository::class);
        $musicRepo = $this->createMock(IMusicRepository::class);

        $musicRepo->method('findMany')->willReturn([]);
        $notificationRepo = $this->createMock(INotificationRepository::class);
        $notificationRepo->method('countUnread')->willReturn(0);

        $service = new UserHeaderService($userRepo, $playlistRepo, $musicRepo, $notificationRepo);
        $controller = new UserHeaderController($service);
        $response = $controller->handle(['action' => 'headerData'], []);

        $this->assertTrue($response['success']);
        $this->assertFalse($response['data']['is_authenticated']);
        $this->assertNull($response['data']['user']);
        $this->assertIsArray($response['data']['playlist_shortcuts']);
    }

    public function testUserHeaderControllerAuthenticatedReturnsUserData(): void
    {
        $userRepo = $this->createMock(IUserProfileRepository::class);
        $playlistRepo = $this->createMock(IPlaylistRepository::class);
        $musicRepo = $this->createMock(IMusicRepository::class);
        $notificationRepo = $this->createMock(INotificationRepository::class);
        $notificationRepo->method('countUnread')->willReturn(2);

        $userId = '018f3a5b6c7d8e9f0123456789abcdef';
        $userRepo->method('findByUserId')->with($userId)->willReturn([
            'id' => $userId,
            'username' => 'bayramali',
            'email' => 'bayram@coremusic.net',
            'role' => 'admin',
        ]);

        $playlistRepo->method('findByUserId')->with($userId)->willReturn([
            ['playlist_id' => '1111', 'name' => 'Favorilerim']
        ]);

        $musicRepo->method('findMany')->willReturn([]);

        $service = new UserHeaderService($userRepo, $playlistRepo, $musicRepo, $notificationRepo);
        $controller = new UserHeaderController($service);
        $request = ['_auth_user' => ['id' => $userId]];

        $response = $controller->handle(['action' => 'headerData'], $request);

        $this->assertTrue($response['success']);
        $this->assertTrue($response['data']['is_authenticated']);
        $this->assertSame('bayramali', $response['data']['user']['username']);
        $this->assertSame('admin', $response['data']['user']['role']);
        $this->assertCount(1, $response['data']['playlist_shortcuts']);
    }

    /** Adım 11: anonim menü boş — hata değil, oturumsuz dropdown'da menü öğesi yok. */
    public function testUserHeaderMenuAnonymousIsEmpty(): void
    {
        $userRepo = $this->createMock(IUserProfileRepository::class);
        $playlistRepo = $this->createMock(IPlaylistRepository::class);
        $musicRepo = $this->createMock(IMusicRepository::class);
        $musicRepo->method('findMany')->willReturn([]);
        $notificationRepo = $this->createMock(INotificationRepository::class);

        $service = new UserHeaderService($userRepo, $playlistRepo, $musicRepo, $notificationRepo);
        $response = $service->getHeaderSummary(null);

        $this->assertSame([], $response['menu']);
    }

    /** Adım 11 Hard Rule 2: rol 'user' → admin menü öğesi YANIT GÖVDESİNDE YOK (sunucu filtresi). */
    public function testUserHeaderMenuServerSideRoleFilter(): void
    {
        $userId = '018f3a5b6c7d8e9f0123456789abcdef';
        $userRepo = $this->createMock(IUserProfileRepository::class);
        $userRepo->method('findByUserId')->willReturn([
            'id' => $userId, 'username' => 'normal', 'email' => 'u@x.test', 'role' => 'user',
        ]);
        $playlistRepo = $this->createMock(IPlaylistRepository::class);
        $playlistRepo->method('findByUserId')->willReturn([]);
        $musicRepo = $this->createMock(IMusicRepository::class);
        $musicRepo->method('findMany')->willReturn([]);
        $notificationRepo = $this->createMock(INotificationRepository::class);

        $service = new UserHeaderService($userRepo, $playlistRepo, $musicRepo, $notificationRepo);
        $menu = $service->getHeaderSummary($userId)['menu'];

        $this->assertCount(2, $menu);
        $ids = array_column($menu, 'id');
        $this->assertSame(['settings', 'history'], $ids);
        $this->assertNotContains('admin', $ids);
    }

    /** Adım 11: rol 'admin' → 3 öğe + şema alanları (id/label/route/roles) tam. */
    public function testUserHeaderMenuAdminRoleIncludesAdminItem(): void
    {
        $userId = '018f3a5b6c7d8e9f0123456789abcdef';
        $userRepo = $this->createMock(IUserProfileRepository::class);
        $userRepo->method('findByUserId')->willReturn([
            'id' => $userId, 'username' => 'admin', 'email' => 'a@x.test', 'role' => 'admin',
        ]);
        $playlistRepo = $this->createMock(IPlaylistRepository::class);
        $playlistRepo->method('findByUserId')->willReturn([]);
        $musicRepo = $this->createMock(IMusicRepository::class);
        $musicRepo->method('findMany')->willReturn([]);
        $notificationRepo = $this->createMock(INotificationRepository::class);

        $service = new UserHeaderService($userRepo, $playlistRepo, $musicRepo, $notificationRepo);
        $menu = $service->getHeaderSummary($userId)['menu'];

        $this->assertCount(3, $menu);
        foreach ($menu as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('route', $item);
            $this->assertArrayHasKey('roles', $item);
            $this->assertContains($item['id'], ['settings', 'history', 'admin']);
        }
        $this->assertSame('https://admin.coremusic.net/', $menu[2]['route']);
    }

    public function testHistoryControllerRecordValidation(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $request = [
            '_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef'],
            'body' => [] // music_id eksik
        ];

        $response = $controller->handle(['action' => 'record'], $request);
        $this->assertArrayHasKey('error', $response);
        $this->assertSame('VALIDATION_ERROR', $response['error']['code']);
    }

    public function testHistoryControllerRecordSuccess(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $userId = '018f3a5b6c7d8e9f0123456789abcdef';
        $musicId = '018f3a5b6c7d8e9f0123456789abcdef';

        $mockRepo->expects($this->once())
            ->method('record')
            ->with($userId, $musicId, 180, 'desktop')
            ->willReturn(true);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $request = [
            '_auth_user' => ['id' => $userId],
            'body' => [
                'music_id' => $musicId,
                'duration_sec' => 180,
                'device_type' => 'desktop'
            ]
        ];

        $response = $controller->handle(['action' => 'record'], $request);
        $this->assertTrue($response['success']);
        $this->assertTrue($response['data']['recorded']);
    }

    /** Adım 08 — limit clamp: >50 istek 50'ye düşer (service katmanı sınırı). */
    public function testHistoryControllerRecentClampsLimitToFifty(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findRecentTracks')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 50)
            ->willReturn([]);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $_GET['limit'] = '999';
        try {
            $response = $controller->handle(
                ['action' => 'recent'],
                ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
            );
        } finally {
            unset($_GET['limit']);
        }

        $this->assertTrue($response['success']);
        $this->assertSame([], $response['data']['items']);
    }

    /** Adım 08 — sayfalı geçmiş: repository'ye page/perPage clamp'i iletilir. */
    public function testHistoryControllerListPassesPagingToService(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findHistory')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 2, 20)
            ->willReturn([['music_hex' => 'aa', 'title' => 'Paged']]);
        $mockRepo->expects($this->once())
            ->method('countForUser')
            ->with('018f3a5b6c7d8e9f0123456789abcdef')
            ->willReturn(41);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $_GET['page'] = '2';
        try {
            $response = $controller->handle(
                ['action' => 'list'],
                ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
            );
        } finally {
            unset($_GET['page']);
        }

        $this->assertTrue($response['success']);
        $this->assertSame(2, $response['data']['page']);
        $this->assertSame(20, $response['data']['per_page']);
        $this->assertSame(41, $response['data']['total']);
        $this->assertSame('Paged', $response['data']['items'][0]['title']);
    }

    /** Adım 08 — ownership: userId yalnız _auth_user'dan gelir, body'den DEĞİL. */
    public function testHistoryControllerRecordIgnoresUserIdInBody(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $sessionUserId = '018f3a5b6c7d8e9f0123456789abcdef';
        $spoofedUserId = 'deadbeefdeadbeefdeadbeefdeadbeef';

        $mockRepo->expects($this->once())
            ->method('record')
            ->with($sessionUserId, $this->anything(), $this->anything(), $this->anything())
            ->willReturn(true);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $request = [
            '_auth_user' => ['id' => $sessionUserId],
            'body' => [
                'music_id' => '018f3a5b6c7d8e9f0123456789abcdef',
                'user_id'  => $spoofedUserId,
            ],
        ];

        $response = $controller->handle(['action' => 'record'], $request);
        $this->assertTrue($response['success']);
    }

    /** Adım 09 — from/to tarih filtresi servise aktarılır + yanıtta `from`/`to` taşınır. */
    public function testHistoryControllerListPassesDateRangeToService(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findHistory')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 1, 20, '2026-10-01', '2026-10-22')
            ->willReturn([
                ['music_hex' => 'aa', 'title' => 'A', 'started_at' => '2026-10-15 10:00:00'],
                ['music_hex' => 'bb', 'title' => 'B', 'started_at' => '2026-10-20 22:30:00'],
            ]);
        $mockRepo->expects($this->once())
            ->method('countForUser')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', '2026-10-01', '2026-10-22')
            ->willReturn(2);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $_GET['from'] = '2026-10-01';
        $_GET['to']   = '2026-10-22';
        try {
            $response = $controller->handle(
                ['action' => 'list'],
                ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
            );
        } finally {
            unset($_GET['from'], $_GET['to']);
        }

        $this->assertTrue($response['success']);
        $this->assertSame('2026-10-01', $response['data']['from']);
        $this->assertSame('2026-10-22', $response['data']['to']);
        $this->assertSame(2, $response['data']['total']);
        $this->assertSame('2026-10-15', $response['data']['items'][0]['day']);
        $this->assertSame('2026-10-20', $response['data']['items'][1]['day']);
    }

    /** Adım 09 — gün gruplama: her item `day` (Y-m-d) alanı alır (başlangıç saatinden). */
    public function testHistoryServiceGroupsItemsByDay(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->method('findHistory')->willReturn([
            ['music_hex' => 'aa', 'started_at' => '2026-10-20 08:15:00'],
            ['music_hex' => 'bb', 'started_at' => '2026-10-20 21:45:00'],
            ['music_hex' => 'cc', 'started_at' => '2026-10-19 12:00:00'],
        ]);
        $mockRepo->method('countForUser')->willReturn(3);

        $service = new HistoryService($mockRepo);
        $result  = $service->getHistory('018f3a5b6c7d8e9f0123456789abcdef');

        $this->assertSame(['2026-10-20', '2026-10-20', '2026-10-19'], array_column($result['items'], 'day'));
    }

    /** Adım 09 — tarih injection kapısı: SQL'e gömülmez; geçersiz format sessizce yok sayılır. */
    public function testHistoryControllerListRejectsMalformedDate(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findHistory')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 1, 20, null, null)
            ->willReturn([]);
        $mockRepo->expects($this->once())->method('countForUser')->willReturn(0);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $_GET['from'] = "2026-10-01' OR '1'='1";
        $_GET['to']   = '2026/10/22';
        try {
            $response = $controller->handle(
                ['action' => 'list'],
                ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
            );
        } finally {
            unset($_GET['from'], $_GET['to']);
        }

        $this->assertTrue($response['success']);
        $this->assertNull($response['data']['from']);
        $this->assertNull($response['data']['to']);
    }

    /** Adım 09 — gelecek tarih kırpılır (trust client YASAK — §9 edge case). */
    public function testHistoryControllerListClampsFutureDateToToday(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->expects($this->once())
            ->method('findHistory')
            ->with('018f3a5b6c7d8e9f0123456789abcdef', 1, 20, date('Y-m-d'), null)
            ->willReturn([]);
        $mockRepo->expects($this->once())->method('countForUser')->willReturn(0);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $_GET['from'] = '2099-01-01';
        try {
            $response = $controller->handle(
                ['action' => 'list'],
                ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
            );
        } finally {
            unset($_GET['from']);
        }

        $this->assertSame(date('Y-m-d'), $response['data']['from']);
    }

    /** Adım 09 — boş geçmiş hata DEĞİL (§9 edge case: boş durum mesajı). */
    public function testHistoryControllerListEmptyHistoryIsSuccess(): void
    {
        $mockRepo = $this->createMock(IHistoryRepository::class);
        $mockRepo->method('findHistory')->willReturn([]);
        $mockRepo->method('countForUser')->willReturn(0);

        $service = new HistoryService($mockRepo);
        $controller = new HistoryController($service);

        $response = $controller->handle(
            ['action' => 'list'],
            ['_auth_user' => ['id' => '018f3a5b6c7d8e9f0123456789abcdef']]
        );

        $this->assertTrue($response['success']);
        $this->assertSame([], $response['data']['items']);
        $this->assertSame(0, $response['data']['total']);
    }
}
