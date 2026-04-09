<?php

declare(strict_types=1);

/**
 * These tests MUST run before any test that calls stubNativephpCall(),
 * because once nativephp_call() is defined it persists for the whole
 * PHP process. Pest loads files alphabetically, so "Bridge..." sorts
 * before "Config..." — do not rename this file past the letter "B".
 *
 * The first test asserts function_exists('nativephp_call') === false
 * as a guard: if file ordering ever changes, that assertion fails
 * immediately rather than silently testing the wrong code path.
 */

use Ikromjon\DocumentScanner\DocumentScanner;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->scanner = new DocumentScanner;
});

describe('bridge unavailable (no stub defined)', function (): void {
    it('returns empty array when nativephp_call does not exist', function (): void {
        expect(function_exists('nativephp_call'))->toBeFalse();

        $result = $this->scanner->scan();

        expect($result)->toBe([]);
    });

    it('logs a warning when nativephp_call does not exist', function (): void {
        Log::spy();

        $this->scanner->scan();

        Log::shouldHaveReceived('warning')
            ->with(Mockery::on(fn (string $msg): bool => str_contains($msg, 'nativephp_call() is not available')))
            ->once();
    });

    it('warning mentions native:run command', function (): void {
        Log::spy();

        $this->scanner->scan();

        Log::shouldHaveReceived('warning')
            ->with(Mockery::on(fn (string $msg): bool => str_contains($msg, 'php artisan native:run')))
            ->once();
    });
});
