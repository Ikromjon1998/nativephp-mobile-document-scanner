# Epic 12: JS Import Ergonomics

**Priority:** Medium
**Status:** Not Started

## Goal

Simplify the JavaScript import path so developers don't need to type the full vendor path.

## Background

The current import requires a long, fragile path:

```js
import { scan, Events } from "../../vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js";
```

This path:
- Breaks if the relative depth changes (e.g., moving the component to a subdirectory)
- Is hard to remember and type
- Looks unprofessional compared to `import { scan } from '@ikromjon/document-scanner'`

## Acceptance Criteria

- [ ] Provide a Vite alias configuration example in docs
- [ ] Alternatively, publish a JS entrypoint to a well-known location
- [ ] Import works with a short alias like `@document-scanner` or similar
- [ ] Vue and React examples updated with the simplified import
- [ ] Backward compatible — long path still works

## Implementation Steps

### Step 1: Document Vite Alias (Quick Win)

Add to docs a recommended `vite.config.js` setup:

```js
// vite.config.js
export default defineConfig({
    resolve: {
        alias: {
            '@document-scanner': '/vendor/ikromjon/nativephp-mobile-document-scanner/resources/js/index.js',
        },
    },
});
```

Then developers can import:
```js
import { scan, Events } from '@document-scanner';
```

### Step 2: Consider Publishable JS Asset (Future)

A `php artisan vendor:publish --tag=document-scanner-js` command that copies the JS file to `resources/js/vendor/document-scanner.js`. This is simpler but creates a copy that could go stale on updates.

### Step 3: Update Examples

Update all Vue/React examples in `docs/javascript.md` and `README.md` to use the alias import with a note about the Vite config.
