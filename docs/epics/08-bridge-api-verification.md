# Epic 8: Bridge API Verification

**Priority:** High
**Status:** Not Started

## Goal

Verify that the native bridge patterns used in this plugin match the current NativePHP Mobile v3 API. The plugin was built with assumptions about bridge APIs that need confirmation against the actual NativePHP source.

## Background

The plugin uses two different patterns for native-to-Laravel event dispatch:

- **Android**: `NativeActionCoordinator.dispatchEvent(activity, eventClass, payload)`
- **iOS**: `LaravelBridge.shared.send?(eventClass, payload)`

These patterns were inferred from examples but may not match the actual NativePHP v3 internal API. If they don't match, events won't be dispatched and the plugin will silently fail.

Additionally:
- **Android** uses `FragmentActivity` cast and `registerForActivityResult()` — need to verify NativePHP's activity type
- **iOS** uses `UIApplication.shared.connectedScenes` to find root view controller — need to verify this works in NativePHP's WebView setup

## Acceptance Criteria

- [ ] Verify `NativeActionCoordinator` class exists and has `dispatchEvent()` method in NativePHP Mobile v3
- [ ] Verify `LaravelBridge.shared.send` exists and accepts `(String, [String: Any])` in NativePHP Mobile v3
- [ ] Verify NativePHP's Android activity is `FragmentActivity` (required for `registerForActivityResult`)
- [ ] Verify iOS root view controller access pattern works in NativePHP's WebView shell
- [ ] Verify `BridgeFunction` interface/protocol matches what the plugin implements
- [ ] Fix any mismatches found
- [ ] Document the verified API patterns for future reference

## Verification Steps

### Step 1: Check NativePHP Mobile Source

1. Inspect `nativephp/mobile` package source code:
   ```bash
   # In a NativePHP app
   ls vendor/nativephp/mobile/
   ```

2. Find the Android bridge classes:
   - Look for `BridgeFunction` interface
   - Look for `NativeActionCoordinator` or equivalent event dispatcher
   - Check the main activity class type

3. Find the iOS bridge classes:
   - Look for `BridgeFunction` protocol
   - Look for `LaravelBridge` or equivalent
   - Check how the main view controller is structured

### Step 2: Compare with Plugin Code

For each pattern, compare:
- Method signatures (parameter types, return types)
- Class/protocol names
- Import paths

### Step 3: Check Official NativePHP Plugins

Reference official plugins for correct patterns:
- `nativephp/mobile-camera` — camera plugin (similar scanner UI pattern)
- `nativephp/mobile-network` — simpler bridge pattern
- `nativephp/mobile-microphone` — audio capture pattern

### Step 4: Fix Mismatches

If any patterns don't match:
1. Update Kotlin code to use correct Android bridge API
2. Update Swift code to use correct iOS bridge API
3. Test on real device to confirm events dispatch correctly

### Step 5: Document Findings

Create a reference document noting:
- The correct Android event dispatch pattern
- The correct iOS event dispatch pattern
- The correct `BridgeFunction` interface signatures
- Any NativePHP version-specific considerations
