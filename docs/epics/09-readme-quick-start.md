# Epic 9: README Quick-Start Improvement

**Priority:** High
**Status:** Done

## Goal

Add a minimal "copy-paste and go" example at the top of the README so developers can start scanning in under 30 seconds without reading the full documentation.

## Background

The current README is comprehensive but verbose for a developer who just wants to try the plugin. The first code example appears after several sections (badges, platform table, features list, installation, configuration). A developer scanning the README has to scroll past ~50 lines before seeing how to use it.

Other popular Laravel packages (Spatie, Filament) lead with a 3-5 line usage snippet before diving into details.

## Acceptance Criteria

- [ ] README starts with a concise quick-start block (install + scan in ~5 lines)
- [ ] Quick-start shows the simplest possible usage (facade + one event listener)
- [ ] Full documentation sections remain below the quick-start
- [ ] No duplication — quick-start links to detailed sections for more info

## Implementation Steps

### Step 1: Add Quick-Start Section

Add immediately after the description/badges:

```markdown
## Quick Start

composer require ikromjon/nativephp-mobile-document-scanner
php artisan native:plugin:register ikromjon/nativephp-mobile-document-scanner

// In your Livewire component:
DocumentScanner::scan();

#[OnNative(DocumentScanned::class)]
public function onScanned($data) {
    $this->paths = $data['paths'];
}
```

### Step 2: Reorganize Existing Sections

Move the detailed "How it works" platform table, features list, and configuration table below the quick-start. Keep them — they're valuable — just not as the first thing a developer sees.
