
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### Queue & Scheduler Setup
```bash
# Start queue worker
php artisan queue:work

# Add to crontab for scheduler
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

## 👥 Demo Users

| Email | Password | Role | Access |
|-------|----------|------|--------|
| admin@unick.test | password | Admin | Full system access |
| planner@unick.test | password | Planner | MRP, forecasting, production planning |
| warehouse@unick.test | password | Warehouse | Inventory, receiving, shipping |
| prod@unick.test | password | Production | Work orders, production tracking |
| customer@unick.test | password | Customer | Product catalog, order placement |

## 📋 Core Modules

### 1. Inventory Management (MRP-based)
- Real-time stock tracking across warehouses
- Automated reorder point calculations
- MRP explosion for demand planning
- Cycle counting and adjustments

### 2. Production Tracking (MRP-II + JIT + Lean)
- Work order management with routing
- Kanban-style production boards
- Capacity planning and utilization
- Backflushing and material consumption

### 3. Integrated Ordering (Customer Portal)
- Product catalog with real-time availability
- Order placement with promised dates
- Order tracking and status updates
- Customer notifications

### 4. Automated Reporting
- Inventory status and turnover reports
- Production efficiency metrics
- Order fulfillment analytics
- Replenishment schedules

## 🔧 Configuration

### Environment Variables
See `.env.example` files in both backend and frontend directories for required environment variables.

### Database
The system uses MySQL with strict foreign keys and soft deletes where appropriate. All tables include proper indexing for performance.

### Scheduling
- **MRP Run**: Daily at 2:00 AM
- **Forecast Recalculation**: Daily at 3:00 AM
- **Report Generation**: Weekly on Sundays at 6:00 AM

## 📊 API Documentation

Access the interactive API documentation at:
- **Swagger UI**: `http://localhost:8000/api/docs`
- **OpenAPI JSON**: `http://localhost:8000/api/docs.json`

## 🧪 Testing

### Backend Tests
```bash
cd backend
php artisan test
```

### Frontend Tests
```bash
cd frontend
npm test
```

## 📁 Project Structure

```
unick-enterprises/
├── backend/                 # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Jobs/
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   └── tests/
├── frontend/                # React application
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── hooks/
│   │   └── services/
│   └── public/
└── README.md
```

## 🔒 Security Features

- Laravel Sanctum SPA authentication
- Role-based access control (RBAC)
- Rate limiting on sensitive endpoints
- Server-side validation
- Audit trails for all transactions

## 📈 Business Logic

### MRP (Material Requirements Planning)
- Daily automated runs
- BOM explosion for demand calculation
- Automatic purchase order generation
- Safety stock considerations

### Forecasting
- Simple Moving Average (SMA-3, SMA-6)
- Consumption rate analysis
- Mean Absolute Deviation (MAD) for confidence
- Reorder point recommendations

### Production Management
- Work order lifecycle management
- Routing-based production steps
- Capacity utilization tracking
- Real-time status updates

## 🚀 Deployment

### Production Setup
1. Configure production environment variables
2. Set up database with proper credentials
3. Configure web server (Apache/Nginx)
4. Set up SSL certificates
5. Configure queue workers and cron jobs
6. Set up monitoring and logging

### Docker (Optional)
Docker Compose files are provided for easy development and deployment.

## 📞 Support

For technical support or feature requests, please contact the development team.

**Unick Enterprises Inc.** - Transforming furniture manufacturing through intelligent ERP so
