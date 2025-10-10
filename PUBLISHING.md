# Publishing to Packagist

This guide explains how to publish the ShopHero MediaCDN PHP client library to Packagist.

## Prerequisites

1. GitHub account
2. Packagist account (https://packagist.org)
3. Composer installed locally

## Steps to Publish

### 1. Create GitHub Repository

First, create a new repository on GitHub:

```bash
# Initialize git in the library directory
cd client-libraries/php/shophero-mediacdn-php
git init
git add .
git commit -m "Initial commit of ShopHero MediaCDN PHP client"

# Create repository on GitHub (https://github.com/new)
# Name: shophero-mediacdn-php
# Then push to GitHub:
git remote add origin git@github.com:ShopHero/shophero-mediacdn-php.git
git branch -M main
git push -u origin main
```

### 2. Tag a Release

Create a version tag:

```bash
git tag -a v1.0.0 -m "Initial release"
git push origin v1.0.0
```

### 3. Submit to Packagist

1. Go to https://packagist.org
2. Log in with your account
3. Click "Submit"
4. Enter the GitHub repository URL: `https://github.com/ShopHero/shophero-mediacdn-php`
5. Click "Check"
6. If everything looks good, click "Submit"

### 4. Set Up Auto-Update (Recommended)

To automatically update Packagist when you push new tags:

1. In your GitHub repository, go to Settings → Webhooks
2. Add webhook:
   - Payload URL: `https://packagist.org/api/github`
   - Content type: `application/json`
   - Secret: Leave empty
   - Events: Just the push event
3. Save the webhook

### 5. Add Badges to README

Add these badges to your README.md:

```markdown
[![Latest Stable Version](https://poser.pugx.org/shophero/mediacdn-php/v)](https://packagist.org/packages/shophero/mediacdn-php)
[![Total Downloads](https://poser.pugx.org/shophero/mediacdn-php/downloads)](https://packagist.org/packages/shophero/mediacdn-php)
[![License](https://poser.pugx.org/shophero/mediacdn-php/license)](https://packagist.org/packages/shophero/mediacdn-php)
[![PHP Version Require](https://poser.pugx.org/shophero/mediacdn-php/require/php)](https://packagist.org/packages/shophero/mediacdn-php)
```

## Releasing New Versions

When you want to release a new version:

1. Update version constraints if needed in `composer.json`
2. Update CHANGELOG.md (create if it doesn't exist)
3. Commit changes
4. Tag the new version:
   ```bash
   git tag -a v1.0.1 -m "Bug fixes and improvements"
   git push origin v1.0.1
   ```

## Version Naming

Follow Semantic Versioning (https://semver.org/):

- MAJOR version (1.x.x): Incompatible API changes
- MINOR version (x.1.x): Add functionality in a backwards compatible manner
- PATCH version (x.x.1): Backwards compatible bug fixes

## Testing Before Release

Always test installation from Packagist:

```bash
# Create a test project
mkdir test-install
cd test-install
composer init --name=test/test --no-interaction
composer require shophero/mediacdn-php
```

## Packagist Best Practices

1. **Keywords**: Keep relevant for discoverability
2. **Description**: Clear and concise
3. **README**: Comprehensive with examples
4. **License**: Always include LICENSE file
5. **Tests**: Ensure tests pass before releasing
6. **PHP Version**: Test with minimum supported version

## Maintenance

- Monitor Packagist statistics
- Respond to issues on GitHub
- Keep dependencies updated
- Follow PSR standards