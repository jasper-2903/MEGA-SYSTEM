# Unick Enterprises Manufacturing System

A production-ready furniture manufacturing ERP system built with Laravel 10+ (backend) and React 18+ (frontend).

## Features

### Core Modules
- **Inventory Management (MRP-based)** - Real-time stock tracking with automated reorder points
- **Production Tracking (MRP-II + JIT + Lean)** - Work order management with capacity planning
- **Integrated Ordering** - Customer web portal with real-time availability
- **Automated Reporting** - Inventory, production, and order analytics

### Technical Stack
- **Backend**: Laravel 10+, PHP 8.4+, MySQL 8, Laravel Sanctum (SPA auth)
- **Frontend**: React 18+, Vite, React Router, Axios, React Query, Bootstrap 5.3
- **Database**: MySQL with Eloquent ORM, foreign keys, soft deletes
- **API**: RESTful with OpenAPI/Swagger documentation
- **Queues**: Laravel database queues for background processing
- **Testing**: PHPUnit (backend), Jest (frontend)

## Quick Start

### Prerequisites
- PHP 8.4+
- Node.js 18+
- MySQL 8.0+
- Composer
- npm

### Backend Setup

1. **Navigate to backend directory**
   ```bash
   cd backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file with your database credentials:**
   ```env
   DB_DATABASE=unick_furniture
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Create database and run migrations**
   ```bash
   mysql -u root -p -e "CREATE DATABASE unick_furniture;"
   php artisan migrate
   ```

6. **Seed demo data**
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

### Frontend Setup

1. **Navigate to frontend directory**
   ```bash
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start development server**
   ```bash
   npm run dev
   ```

### Queue Processing (Required for MRP & Reports)

Start the queue worker in a separate terminal:
```bash
cd backend
php artisan queue:work
```

### Scheduling (For Automated Jobs)

Add to your crontab:
```bash
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

## Demo Users

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| Admin | admin@unick.test | password | Full system access |
| Planner | planner@unick.test | password | MRP, forecasting, production planning |
| Warehouse | warehouse@unick.test | password | Inventory, receiving, shipping |
| Production | prod@unick.test | password | Work orders, production logging |
| Customer | customer@unick.test | password | Product catalog, order placement |

## API Documentation

Access the interactive API documentation at:
```
http://localhost:8000/api/docs
```

## Database Schema

### Core Entities
- **Products**: Furniture items with dimensions, pricing, BOMs
- **Materials**: Raw materials and components with supplier info
- **BOMs**: Bill of Materials with revision control
- **Inventory**: Multi-location stock tracking
- **Work Centers**: Production facilities with capacity
- **Sales/Purchase Orders**: Order management with line items

### Key Features
- **MRP Engine**: Material Requirements Planning with lead times
- **Forecasting**: SMA-3, SMA-6, and consumption-rate models
- **Production Routing**: Multi-step manufacturing processes
- **Inventory Transactions**: Complete audit trail
- **Role-based Access**: 5-tier permission system

## Business Logic

### MRP (Material Requirements Planning)
- Daily automated runs via Laravel Scheduler
- BOM explosion for demand calculation
- Safety stock and reorder point consideration
- Automatic purchase order generation

### Production Tracking
- Kanban-style work order management
- Real-time capacity utilization monitoring
- Backflushing for material consumption
- OEE-style efficiency reporting

### Inventory Management
- Multi-warehouse, multi-location tracking
- Cycle counting with approval workflows
- Real-time allocation and availability
- Automated reorder suggestions

### Customer Portal
- Public product catalog with search/filtering
- Real-time availability and promise dates
- Order tracking with status updates
- Account management for registered customers

## Development

### Running Tests

**Backend (PHPUnit):**
```bash
cd backend
php artisan test
```

**Frontend (Jest):**
```bash
cd frontend
npm test
```

### Code Quality

**Backend (PHPStan Level 6+):**
```bash
cd backend
./vendor/bin/phpstan analyse
```

**Frontend (ESLint + Prettier):**
```bash
cd frontend
npm run lint
npm run format
```

### Building for Production

**Backend:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Frontend:**
```bash
npm run build
```

## Architecture

### Monorepo Structure
```
/
├── backend/         # Laravel API application
├── frontend/        # React SPA application
├── README.md        # This file
└── .env.example     # Environment template
```

### API Design
- RESTful endpoints with consistent naming
- JSON responses with proper HTTP status codes
- Laravel Sanctum for SPA authentication
- Request validation and API resources
- Rate limiting and error handling

### Frontend Architecture
- Component-based React structure
- React Query for server state management
- Bootstrap 5.3 for responsive UI
- Formik + Yup for form handling
- Axios for HTTP client

## Deployment

### Environment Variables

Create production `.env` files:

**Backend (.env):**
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
CACHE_DRIVER=database
SESSION_DRIVER=database
```

### Web Server Configuration

Configure your web server to:
1. Point to `backend/public` for API routes
2. Serve React build files for frontend routes
3. Enable HTTPS in production
4. Set up appropriate file permissions

### Background Services

Ensure these services are running:
1. Queue worker: `php artisan queue:work`
2. Scheduler: Add cron entry for `php artisan schedule:run`
3. MySQL database server

## Contributing

1. Follow PSR-12 coding standards for PHP
2. Use ESLint configuration for JavaScript
3. Write tests for new features
4. Update API documentation for endpoint changes
5. Follow semantic versioning for releases

## License

Proprietary - Unick Enterprises Inc.

## Support

For technical support or questions:
- Email: admin@unick.com
- Documentation: http://localhost:8000/api/docs
- System Requirements: PHP 8.4+, Node.js 18+, MySQL 8.0+