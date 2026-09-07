#!/bin/bash

# Hentikan eksekusi jika ada error
set -e

echo "=========================================="
echo "🚀 Memulai Proses Deployment Diginiaga..."
echo "=========================================="

# 1. Masuk ke mode maintenance (Tampilkan halaman "Sedang perbaikan")
echo "🔒 Mengaktifkan Mode Maintenance..."
php artisan down --refresh=15 --secret="diginiaga-bypass-$(date +%s)" || true

# 2. Tarik kode terbaru dari GitHub
BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "📥 Mengambil update dari branch $BRANCH..."
git reset --hard
git pull origin $BRANCH

# 3. Update dependencies PHP
echo "📦 Menginstal dependencies PHP (Composer)..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 4. Update & Build aset Frontend (Vite/Tailwind)
echo "📦 Menginstal dependencies Node (NPM)..."
npm ci
echo "🏗️ Membangun aset statis (Vite)..."
npm run build

# 5. Jalankan Migrasi Database
echo "🗄️ Menjalankan migrasi database..."
php artisan migrate --force

# 6. Optimize & Cache
echo "🧹 Membersihkan dan membuat ulang cache..."
php artisan optimize:clear
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 7. Restart Queue Worker (Jika menggunakan queue)
echo "🔄 Merestart Queue Worker..."
php artisan queue:restart || true

# 8. Matikan mode maintenance
echo "🔓 Mematikan Mode Maintenance..."
php artisan up

echo "=========================================="
echo "✅ Deployment Berhasil Selesai!"
echo "=========================================="
