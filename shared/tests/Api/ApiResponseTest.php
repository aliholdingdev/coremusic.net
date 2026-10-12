<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use PHPUnit\Framework\TestCase;
use CoreMusic\Api\ApiResponse;

final class ApiResponseTest extends TestCase
{
    public function testOkReturnsDataKey(): void
    {
        $result = ApiResponse::ok(['id' => 1, 'name' => 'test']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(1, $result['data']['id']);
        $this->assertEquals('test', $result['data']['name']);
    }

    public function testOkReturnsMetaWithTimestamp(): void
    {
        $result = ApiResponse::ok();
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('timestamp', $result['meta']);
        $this->assertArrayHasKey('version', $result['meta']);
    }

    public function testErrorReturnsErrorStructure(): void
    {
        $result = ApiResponse::error('NOT_FOUND', 'Resource not found', 404);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('NOT_FOUND', $result['error']['code']);
        $this->assertEquals('Resource not found', $result['error']['message']);
    }

    public function testErrorIncludesDetailsWhenProvided(): void
    {
        $details = ['field' => 'email', 'reason' => 'invalid format'];
        $result = ApiResponse::error('VALIDATION_ERROR', 'Invalid input', 422, $details);
        $this->assertArrayHasKey('details', $result['error']);
        $this->assertEquals($details, $result['error']['details']);
    }

    public function testErrorExcludesDetailsWhenEmpty(): void
    {
        $result = ApiResponse::error('BAD_REQUEST', 'Invalid', 400);
        $this->assertArrayNotHasKey('details', $result['error']);
    }

    public function testPaginatedReturnsCorrectPagination(): void
    {
        $result = ApiResponse::paginated([1, 2, 3], 2, 10, 25);
        $this->assertEquals(2, $result['meta']['pagination']['page']);
        $this->assertEquals(10, $result['meta']['pagination']['pageSize']);
        $this->assertEquals(25, $result['meta']['pagination']['totalItems']);
        $this->assertEquals(3, $result['meta']['pagination']['totalPages']);
    }

    public function testNoContentReturnsEmptyArray(): void
    {
        $result = ApiResponse::noContent();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCreatedReturnsData(): void
    {
        $result = ApiResponse::created(['id' => 42, 'name' => 'New Item']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(42, $result['data']['id']);
    }
}
