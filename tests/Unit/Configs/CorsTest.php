<?php

namespace Tests\Unit\Configs;

use App\Ship\Tests\ShipTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class CorsTest extends ShipTestCase
{
    public function testConfigHasCorrectValues(): void
    {
        $config = config('cors');
        $expectedOrigins = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env(
                'CORS_ALLOWED_ORIGINS',
                env('FRONTEND_URL', 'http://localhost:5173'),
            )),
        )));

        $expected = [
            'paths' => ['*', 'sanctum/csrf-cookie'],
            'allowed_methods' => ['*'],
            'allowed_origins' => $expectedOrigins,
            'allowed_origins_patterns' => [],
            'allowed_headers' => ['*'],
            'exposed_headers' => [],
            'max_age' => 0,
            'supports_credentials' => false,
        ];

        $this->assertEqualsCanonicalizing($expected, $config);
    }
}
