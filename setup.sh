#!/bin/bash

echo "🚀 Setting up Unick Enterprises ERP System"
echo "=========================================="

# Check if required software is installed
echo "📋 Checking prerequisites..."

if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2+"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer"
    exit 1
fi

if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18+"
    exit 1
fi

if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL is not installed. Please install MySQL 8.0+"
    exit 1
fi

echo "✅ Prerequisites check passed"

# Backend Setup
echo ""
echo "🔧 Setting up Backend (Laravel)"
echo "================================"

cd backend

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-interaction

# Copy environment file
if [ ! -f .env ]; then
    echo "📄 Creating .env file..."
    cp .env.example .env
    echo "⚠️  Please configure your database settings in backend/.env"
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Create database if it doesn't exist
echo "🗄️  Setting up database..."
php artisan db:create 2>/dev/null || echo "Database already exists or cannot be created"

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database with sample data..."
php artisan db:seed --force

# Install Laravel Sanctum
echo "🔐 Installing Laravel Sanctum..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Create storage link
echo "📁 Creating storage link..."
php artisan storage:link

# Set permissions
echo "🔒 Setting file permissions..."
chmod -R 775 storage bootstrap/cache

cd ..

# Frontend Setup
echo ""
echo "🎨 Setting up Frontend (React)"
echo "==============================="

cd frontend

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Build frontend for production
echo "🏗️  Building frontend..."
npm run build

cd ..

echo ""
echo "✅ Setup Complete!"
echo "=================="
echo ""
echo "🎉 Unick Enterprises ERP System is ready!"
echo ""
echo "📋 Next Steps:"
echo "1. Configure your database settings in backend/.env"
echo "2. Start the backend server: cd backend && php artisan serve"
echo "3. Start the frontend server: cd frontend && npm run dev"
echo "4. Access the application at http://localhost:3000"
echo ""
echo "👥 Demo Users:"
echo "- Admin: admin@unick.test / password"
echo "- Planner: planner@unick.test / password"
echo "- Warehouse: warehouse@unick.test / password"
echo "- Production: prod@unick.test / password"
echo "- Customer: customer@unick.test / password"
echo ""
echo "🔧 Additional Configuration:"
echo "- Set up cron jobs for scheduled tasks"
echo "- Configure queue workers: php artisan queue:work"
echo "- Set up email configuration for notifications"
echo ""
echo "📚 Documentation:"
echo "- API Documentation: http://localhost:8000/api/docs"
echo "- README.md for detailed setup instructions"
echo ""
echo "🚀 Happy manufacturing!"