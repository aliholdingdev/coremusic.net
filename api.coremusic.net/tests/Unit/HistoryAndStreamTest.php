<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Controller\HistoryController;
use CoreMusic\Api\Controller\UserHeaderController;
use CoreMusic\Api\Repository\IHistoryRepository;
use CoreMusic\Api\Repository\IMusicRepository;
use CoreMusic\Api\Repository\IPlaylistRepository;
use CoreMusic\Api\Repository\IUserProfileRepository;
use CoreMusic\Api\Service\HistoryService;
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

        $controller = new UserHeaderController($userRepo, $playlistRepo, $musicRepo);
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

        $controller = new UserHeaderController($userRepo, $playlistRepo, $musicRepo);
        $request = ['_auth_user' => ['id' => $userId]];

        $response = $controller->handle(['action' => 'headerData'], $request);

        $this->assertTrue($response['success']);
        $this->assertTrue($response['data']['is_authenticated']);
        $this->assertSame('bayramali', $response['data']['user']['username']);
        $this->assertSame('admin', $response['data']['user']['role']);
        $this->assertCount(1, $response['data']['playlist_shortcuts']);
    }
}
