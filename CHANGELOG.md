# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2025-07-10

### Added
- Support for truncated base64url signatures (22 characters instead of 64)
- New `useLegacySignature()` method to opt-in to old hex format
- Full backward compatibility with existing signed URLs

### Changed
- Default signature format is now truncated base64url for shorter URLs
- URLs are now 42 characters shorter (16% overall reduction)

### Technical Details
- Uses 128-bit truncated HMAC-SHA256 for security/length balance
- Base64url encoding without padding for URL safety
- Server validates both old and new signature formats automatically

## [1.1.1] - 2025-07-07

### Fixed
- Fixed signature generation to sort query parameters alphabetically for consistent signing
- Ensures compatibility with Lambda@Edge URL validation

## [1.1.0] - 2025-07-07

### Changed
- Removed `source` parameter from signed URLs - source is now derived from subdomain
- Updated `SignedUrlBuilder` constructor to only require domain, path, and PSK
- Updated `MediaCDNClient` to remove `sourceId` property and parameter
- Updated all examples to use generic example domains instead of actual endpoints

### Improved
- Cleaner URLs without redundant source parameter
- Simplified SDK initialization for signed URLs

## [1.0.0] - 2024-01-04

### Added
- Initial release of ShopHero MediaCDN PHP client
- Basic URL building with transformation parameters
- Signed URL generation with PSK-based security
- Source management API client
- Support for all major image transformations:
  - Resize (width, height)
  - Quality adjustment
  - Format conversion (auto, jpeg, png, webp, avif)
  - Fit modes (crop, cover, contain, fill, scale, pad)
  - Device pixel ratio (DPR)
- Comprehensive test suite
- Examples for common use cases
- Full documentation

### Security
- HMAC-SHA256 signature validation
- Expiring URLs support
- PSK rotation capability

[Unreleased]: https://github.com/ShopHero/shophero-mediacdn-php/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/ShopHero/shophero-mediacdn-php/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/ShopHero/shophero-mediacdn-php/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/ShopHero/shophero-mediacdn-php/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/ShopHero/shophero-mediacdn-php/releases/tag/v1.0.0