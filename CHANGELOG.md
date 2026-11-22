# Changelog

All notable changes to this project are documented in this file.

## [1.0.1] - Unreleased

### Fixed
- Corrected the XLSX writer namespace so Composer autoloading resolves the writer.
- Preserved eager-loaded Eloquent relationships by using chunked lazy query iteration.
- Ensured XLSX text values are written as strings instead of being interpreted as formulas.
- Added CSV formula-injection mitigation for values beginning with spreadsheet formula markers.
- Ensured generated temporary files and PhpSpreadsheet worksheets are cleaned up after success or failure.
- Sanitized download filenames and added filesystem storage support through `store()`.
- Fixed CI dependency resolution so PHP 8.2 jobs can select compatible transitive package versions.

### Added
- Regression tests for XLSX generation and CSV formula-injection mitigation.
- Laravel 13 to the CI compatibility matrix.

### Notes
- XLSX generation still uses PhpSpreadsheet and is not constant-memory streaming.
- Queue-based exports and export progress tracking are not part of this release.
