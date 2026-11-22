<?php

declare(strict_types=1);

namespace Ikromjon\DocumentScanner\Tests\Fixtures;

use Ikromjon\DocumentScanner\Events\DocumentScanned;
use Ikromjon\DocumentScanner\Events\PdfCreated;
use Ikromjon\DocumentScanner\Events\ScanCancelled;
use Ikromjon\DocumentScanner\Events\ScanFailed;

/**
 * Every payload the native code posts to `/_native/api/events`, as
 * [event class, payload] pairs, transcribed from the dispatch sites:
 *
 * iOS (resources/ios/Sources/DocumentScannerFunctions.swift)
 * - documentCameraViewController(_:didFinishWith:)       DocumentScanned, ScanFailed
 * - documentCameraViewControllerDidCancel                ScanCancelled
 * - documentCameraViewController(_:didFailWithError:)   ScanFailed
 * - ImagesToPdf                                          PdfCreated
 *
 * Android (resources/android/src/DocumentScannerFunctions.kt)
 * - registerLauncher, result not OK                      ScanCancelled
 * - handleScanResult                                     DocumentScanned, ScanFailed
 * - Scan, scanner failed to start                        ScanFailed
 * - ImagesToPdf                                          PdfCreated
 *
 * NativePHP builds the event with the payload as named constructor arguments,
 * so a key the constructor does not declare throws "Unknown named parameter" on
 * nativephp/mobile 3.3.x and is dropped on 4.6.1+, and a missing required
 * parameter throws on every version.
 *
 * Change this list whenever a native dispatch site changes.
 */
final class NativeEventPayloads
{
    /**
     * @return array<string, array{0: class-string, 1: array<string, mixed>}>
     */
    public static function all(): array
    {
        $storage = '/var/mobile/Containers/Data/Application/APP/Documents/scanned-documents';

        return [
            'DocumentScanned as JPEG pages' => [DocumentScanned::class, [
                'paths' => [$storage.'/scan_1763800000000_0.jpg', $storage.'/scan_1763800000000_1.jpg'],
                'pageCount' => 2,
                'outputFormat' => 'jpeg',
            ]],
            'DocumentScanned as a PDF' => [DocumentScanned::class, [
                'paths' => [$storage.'/scan_1763800000000.pdf'],
                'pageCount' => 3,
                'outputFormat' => 'pdf',
            ]],
            // iOS skips pages it cannot write, so the list can come back empty.
            'DocumentScanned with no pages written' => [DocumentScanned::class, [
                'paths' => [],
                'pageCount' => 0,
                'outputFormat' => 'jpeg',
            ]],
            'ScanCancelled' => [ScanCancelled::class, []],
            'ScanFailed' => [ScanFailed::class, [
                'error' => 'Failed to retrieve scan result',
            ]],
            'PdfCreated' => [PdfCreated::class, [
                'path' => $storage.'/document.pdf',
            ]],
        ];
    }
}
