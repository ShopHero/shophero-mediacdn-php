# GitHub Repository Setup Guide

This guide walks you through publishing the ShopHero MediaCDN PHP SDK to GitHub for Composer installation.

## Steps to Publish

### 1. Create GitHub Repository

1. Go to https://github.com/ShopHero (or your organization)
2. Click "New repository"
3. Repository name: `shophero-mediacdn-php`
4. Description: "PHP client library for ShopHero MediaCDN - High-performance image CDN with on-the-fly transformations"
5. Set to Public (for easier Composer access)
6. Don't initialize with README (we already have one)

### 2. Push to GitHub

From this directory (`/Users/mattgarner/DumacSites/shophero-mediacdn-v1/client-libraries/php/shophero-mediacdn-php/`):

```bash
# Add the GitHub remote (replace with your actual repo URL)
git remote add origin https://github.com/ShopHero/shophero-mediacdn-php.git

# Push main branch and tags
git push -u origin main
git push origin --tags
```

### 3. Verify Installation

Test that the package can be installed via Composer:

```bash
# Create a test project
mkdir test-install && cd test-install

# Create composer.json
cat > composer.json << 'EOF'
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ShopHero/shophero-mediacdn-php"
        }
    ],
    "require": {
        "shophero/mediacdn-php": "^1.0"
    }
}
EOF

# Install the package
composer install
```

## Usage for End Users

Once published, users can install the package by adding this to their `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ShopHero/shophero-mediacdn-php"
        }
    ],
    "require": {
        "shophero/mediacdn-php": "^1.0"
    }
}
```

Or install directly:

```bash
composer require shophero/mediacdn-php:^1.0
```

## Repository Structure

The repository is now set up with:

- ✅ `composer.json` with proper autoloading
- ✅ PSR-4 namespace structure
- ✅ Comprehensive test suite
- ✅ Examples and documentation
- ✅ MIT license
- ✅ Semantic versioning (v1.0.0)
- ✅ GitHub-ready README with installation instructions
- ✅ CHANGELOG for version tracking

## Maintenance

### Releasing New Versions

1. Update `CHANGELOG.md`
2. Commit changes
3. Tag the new version: `git tag v1.1.0`
4. Push: `git push origin main --tags`

### Branch Strategy

- `main` branch for stable releases
- Feature branches for development
- Tags for version releases (v1.0.0, v1.1.0, etc.)