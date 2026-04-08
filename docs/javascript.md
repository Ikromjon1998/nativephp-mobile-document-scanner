# Usage with JavaScript (Inertia Vue/React)

## Import

```js
import {
  scan,
  Events,
} from "../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js";
import { On } from "#nativephp";
```

## API

### `scan(options?)`

Opens the native scanner UI. Returns a promise that resolves when the scanner opens (not when scanning completes — results arrive via events).

```js
await scan(); // defaults
await scan({ maxPages: 3 }); // limit pages
await scan({ outputFormat: "pdf" }); // output as PDF
await scan({ maxPages: 1, jpegQuality: 95 }); // high-quality single page
```

**Parameters:**

| Option         | Type    | Default  | Description                                    |
| -------------- | ------- | -------- | ---------------------------------------------- |
| `maxPages`     | number  | `0`      | Max pages (0 = unlimited)                      |
| `outputFormat` | string  | `'jpeg'` | `'jpeg'` or `'pdf'`                            |
| `jpegQuality`  | number  | `90`     | JPEG quality 1-100                             |
| `galleryImport`| boolean | `false`  | Allow gallery import (Android only)            |
| `scannerMode`  | string  | `'full'` | `'base'`, `'filter'`, or `'full'` (Android only) |

### `Events`

Event name constants for the `On()` listener:

```js
Events.DocumentScanned; // scan completed
Events.ScanCancelled; // user cancelled
Events.ScanFailed; // error occurred
```

## Event Payloads

### DocumentScanned

```js
On(Events.DocumentScanned, (payload) => {
  payload.paths; // ['/path/to/scan_0.jpg', '/path/to/scan_1.jpg']
  payload.pageCount; // 2
  payload.outputFormat; // 'jpeg'
});
```

### ScanCancelled

```js
On(Events.ScanCancelled, () => {
  // no payload
});
```

### ScanFailed

```js
On(Events.ScanFailed, (payload) => {
  payload.error; // 'Camera access denied'
});
```

## Vue 3 Example

```vue
<script setup>
import { ref } from "vue";
import { On } from "#nativephp";
import {
  scan,
  Events,
} from "../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js";

const pages = ref([]);
const error = ref(null);
const scanning = ref(false);

async function startScan() {
  error.value = null;
  scanning.value = true;
  await scan({ maxPages: 5, outputFormat: "jpeg", jpegQuality: 85 });
}

On(Events.DocumentScanned, (payload) => {
  pages.value = payload.paths;
  scanning.value = false;
});

On(Events.ScanCancelled, () => {
  scanning.value = false;
});

On(Events.ScanFailed, (payload) => {
  error.value = payload.error;
  scanning.value = false;
});
</script>

<template>
  <div>
    <button @click="startScan" :disabled="scanning">
      {{ scanning ? "Scanning..." : "Scan Document" }}
    </button>

    <p v-if="error" class="error">{{ error }}</p>

    <ul v-if="pages.length">
      <li v-for="(path, i) in pages" :key="i">{{ path }}</li>
    </ul>
  </div>
</template>
```

## React Example

```jsx
import { useState } from "react";
import { On } from "#nativephp";
import {
  scan,
  Events,
} from "../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js";

export default function Scanner() {
  const [pages, setPages] = useState([]);
  const [error, setError] = useState(null);
  const [scanning, setScanning] = useState(false);

  async function startScan() {
    setError(null);
    setScanning(true);
    await scan({ maxPages: 5, outputFormat: "jpeg", jpegQuality: 85 });
  }

  On(Events.DocumentScanned, (payload) => {
    setPages(payload.paths);
    setScanning(false);
  });

  On(Events.ScanCancelled, () => {
    setScanning(false);
  });

  On(Events.ScanFailed, (payload) => {
    setError(payload.error);
    setScanning(false);
  });

  return (
    <div>
      <button onClick={startScan} disabled={scanning}>
        {scanning ? "Scanning..." : "Scan Document"}
      </button>

      {error && <p className="error">{error}</p>}

      {pages.length > 0 && (
        <ul>
          {pages.map((path, i) => (
            <li key={i}>{path}</li>
          ))}
        </ul>
      )}
    </div>
  );
}
```

## PDF Scanning Example

```js
async function scanToPdf() {
  await scan({ outputFormat: "pdf" });
}

On(Events.DocumentScanned, (payload) => {
  // PDF mode: single file with all pages
  const pdfPath = payload.paths[0];
  console.log(`PDF saved at ${pdfPath} with ${payload.pageCount} pages`);
});
```
