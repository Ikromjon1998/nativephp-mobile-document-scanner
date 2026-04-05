const baseUrl = '/_native/api/call';

/**
 * Call a native bridge function.
 *
 * @param {string} method - Bridge function name (e.g. 'DocumentScanner.Scan')
 * @param {Object} params - Parameters to pass to the bridge function
 * @returns {Promise<Object>} The response data from the native bridge
 */
async function bridgeCall(method, params = {}) {
    const response = await fetch(baseUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify({ method, params }),
    });

    const result = await response.json();

    if (result.status === 'error') {
        throw new Error(result.message || 'Native call failed');
    }

    return result.data;
}

// ---------------------------------------------------------------------------
// Bridge Functions
// ---------------------------------------------------------------------------

/**
 * Open the document scanner.
 *
 * Results are delivered asynchronously via events:
 * - DocumentScanned: scanning completed (paths, pageCount, outputFormat)
 * - ScanCancelled: user cancelled the scanner
 * - ScanFailed: an error occurred (error message)
 *
 * @param {Object} [options]
 * @param {number} [options.maxPages=0] - Max pages to scan (0 = unlimited)
 * @param {string} [options.outputFormat='jpeg'] - Output format: 'jpeg' or 'pdf'
 * @param {number} [options.jpegQuality=90] - JPEG quality (1-100)
 * @param {boolean} [options.galleryImport=false] - Allow gallery import (Android only)
 * @param {string} [options.scannerMode='full'] - Scanner mode: 'base', 'filter', or 'full' (Android only)
 * @returns {Promise<{success: boolean, error?: string}>}
 */
export async function scan(options = {}) {
    return bridgeCall('DocumentScanner.Scan', options);
}

// ---------------------------------------------------------------------------
// Event Constants
// ---------------------------------------------------------------------------

/**
 * Event name constants for use with the NativePHP `On()` listener.
 *
 * @example
 * import { On } from '#nativephp';
 * import { Events } from '../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js';
 *
 * On(Events.DocumentScanned, (payload) => {
 *     console.log('Scanned:', payload.paths, payload.pageCount);
 * });
 */
export const Events = {
    DocumentScanned: 'Ikromjon\\DocumentScanner\\Events\\DocumentScanned',
    ScanCancelled: 'Ikromjon\\DocumentScanner\\Events\\ScanCancelled',
    ScanFailed: 'Ikromjon\\DocumentScanner\\Events\\ScanFailed',
};
