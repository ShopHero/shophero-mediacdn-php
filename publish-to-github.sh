#!/bin/bash
set -e

# Configuration
REPO_URL="git@github.com:ShopHero/shophero-mediacdn-php.git"
VERSION="1.1.1"

echo "📦 Preparing to publish ShopHero MediaCDN PHP SDK v${VERSION}"
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found. Please run this script from the PHP SDK directory."
    exit 1
fi

# Initialize git if needed
if [ ! -d ".git" ]; then
    echo "📂 Initializing git repository..."
    git init
    git remote add origin "$REPO_URL"
fi

# Create .gitignore if it doesn't exist
if [ ! -f ".gitignore" ]; then
    echo "📝 Creating .gitignore..."
    cat > .gitignore << 'EOF'
/vendor/
/.phpunit.cache/
/.idea/
/.vscode/
/composer.lock
/.DS_Store
/coverage/
/.env
EOF
fi

# Add all files
echo "📄 Adding files to git..."
git add -A

# Show what will be committed
echo ""
echo "📋 Files to be committed:"
git status --porcelain

# Commit
echo ""
echo "💾 Creating commit..."
git commit -m "Release v${VERSION}

- Removed source parameter from signed URLs (source now derived from subdomain)
- Simplified SDK initialization for signed URLs
- Updated examples to use generic domains"

# Create tag
echo "🏷️  Creating tag v${VERSION}..."
git tag -a "v${VERSION}" -m "Version ${VERSION}"

# Push to GitHub
echo ""
echo "🚀 Pushing to GitHub..."
echo "This will push to: $REPO_URL"
echo ""
read -p "Continue? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    git push origin main --tags
    echo ""
    echo "✅ Successfully published v${VERSION} to GitHub!"
    echo ""
    echo "📌 Next steps:"
    echo "1. Go to https://github.com/ShopHero/shophero-mediacdn-php/releases"
    echo "2. Click 'Draft a new release'"
    echo "3. Select tag v${VERSION}"
    echo "4. Set release title to 'v${VERSION}'"
    echo "5. Copy the changelog entries for this version"
    echo "6. Publish the release"
else
    echo "❌ Cancelled"
    exit 1
fi