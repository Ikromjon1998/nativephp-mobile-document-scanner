# Roadmap

Feature roadmap for the NativePHP Mobile Document Scanner plugin.

## Epic Status

### Developer Experience (Priority)

| # | Epic | Priority | Status | Doc |
|---|------|----------|--------|-----|
| 9 | README Quick-Start | High | **Done** | [docs/epics/09-readme-quick-start.md](docs/epics/09-readme-quick-start.md) |
| 10 | Silent Failure Handling | High | **Done** | [docs/epics/10-silent-failure-handling.md](docs/epics/10-silent-failure-handling.md) |
| 11 | Platform-Specific Option Hints | Medium | **Done** | [docs/epics/11-platform-option-hints.md](docs/epics/11-platform-option-hints.md) |
| 12 | JS Import Ergonomics | Medium | Not Started | [docs/epics/12-js-import-ergonomics.md](docs/epics/12-js-import-ergonomics.md) |
| 13 | maxPages Null Convention | Low | Not Started | [docs/epics/13-max-pages-null-convention.md](docs/epics/13-max-pages-null-convention.md) |

### Features (Planned)

| # | Epic | Priority | Status | Doc |
|---|------|----------|--------|-----|
| 4 | Scanned File Management | Medium | Not Started | [docs/epics/04-file-management.md](docs/epics/04-file-management.md) |
| 5 | Scan Result DTO | Low | Not Started | [docs/epics/05-scan-result-dto.md](docs/epics/05-scan-result-dto.md) |
| 6 | Image Post-Processing | Low | Not Started | [docs/epics/06-image-post-processing.md](docs/epics/06-image-post-processing.md) |
| 3 | iOS Max Pages Enforcement | Low | Not Started | [docs/epics/03-ios-max-pages.md](docs/epics/03-ios-max-pages.md) |
| 7 | Real Device Test App | Low | Not Started | [docs/epics/07-test-app.md](docs/epics/07-test-app.md) |

### Completed

| # | Epic | Doc |
|---|------|-----|
| 1 | Gallery Import | [docs/epics/01-gallery-import.md](docs/epics/01-gallery-import.md) |
| 2 | Scanner Mode Selection | [docs/epics/02-scanner-mode.md](docs/epics/02-scanner-mode.md) |
| 8 | Bridge API Verification | [docs/epics/08-bridge-api-verification.md](docs/epics/08-bridge-api-verification.md) |
| 14 | JPEG-to-PDF Conversion | [docs/epics/14-jpeg-to-pdf-conversion.md](docs/epics/14-jpeg-to-pdf-conversion.md) |
| 15 | Page Thumbnail Extraction from PDF | [docs/epics/15-page-thumbnail-extraction.md](docs/epics/15-page-thumbnail-extraction.md) |

## Release History

- v1.4.0: JPEG-to-PDF conversion, PDF page thumbnails (Epics 14 & 15)
- v1.3.0: Warning log, quick-start README, real native linting, coverage 95% (Epics 9 & 10)
- v1.2.0: Scanner mode selection (Epic 2)
- v1.1.0: Gallery import (Epic 1)
- v1.0.0: Core scanning (JPEG/PDF), events, config, validation, tests, CI/CD, docs
