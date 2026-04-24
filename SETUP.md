# Quick Setup Guide - Salsabila Track

## 1. Install Dependencies
```bash
composer install
```

## 2. Configure Database
Create MySQL database:
```sql
CREATE DATABASE salsabila_delivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update `.env`:
```env
DB_DATABASE=salsabila_delivery
DB_USERNAME=root
DB_PASSWORD=your_password
```

## 3. Generate Key & Migrate
```bash
php artisan key:generate
php artisan migrate
```

## 4. Start Server
```bash
php artisan serve
```

## 5. Access Application
- Admin: http://localhost:8000/admin
- Tracking: http://localhost:8000/tracking/ORD-001

---

## Database Schema (orders table)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) PK | Auto-increment ID |
| order_number | varchar(20) | Unique order code (ORD-001) |
| customer_name | varchar(255) | Customer's name |
| order_items | text | Ordered items description |
| status | enum | waiting, processing, shipped, completed |
| order_time | varchar(20) | Time placed (HH:mm) |
| started_at | timestamp | Processing start time |
| completed_at | timestamp | Completion time |
| duration_minutes | int | Delivery duration |
| is_late | boolean | Over 60 min flag |
| created_at | timestamp | Record created |
| updated_at | timestamp | Last updated |

---

## API Endpoints

```
GET    /admin                              → Admin dashboard
POST   /admin/orders                       → Create new order (JSON)
POST   /admin/orders/{id}/status/{status}  → Update order status
GET    /admin/orders/json                  → Get all orders (JSON)
GET    /tracking/{orderNumber}             → Customer tracking page
```

---

## Status Flow

```
waiting (1) → processing (2) → shipped (3) → completed (4)
   ↓            ↓               ↓             ↓
Menunggu   Diproses      Dikirim       Selesai
```

---

## Project Structure

```
salsabilatrack/
├── app/
│   ├── Http/Controllers/
│   │   ├── OrderController.php      # Admin order management
│   │   └── TrackingController.php   # Customer tracking
│   └── Models/Order.php             # Order Eloquent model
├── database/migrations/
│   └── create_orders_table.php      # Database schema
├── resources/views/
│   ├── layouts/app.blade.php        # Main layout (Tailwind CSS)
│   ├── admin/dashboard.blade.php    # Admin panel
│   └── tracking/
│       ├── index.blade.php          # Tracking display
│       └── not-found.blade.php      # 404 page
├── routes/web.php                   # Web routes
├── composer.json                    # PHP dependencies
├── .env.example                     # Environment template
└── README.md                        # Full documentation
```

---

## Design Features

- **Colors**: Orange primary (#f97316), probability gradients
- **Typography**: Playfair Display + Inter fonts
- **Effects**: Glassmorphism cards, soft shadows, smooth animations
- **Responsive**: Mobile-first design

---

## File Completed Count

✅ 25+ files created
✅ Database migration + model
✅ 2 Controllers with full CRUD
✅ 3 Views (admin, tracking, not-found)
✅ Responsive Tailwind CSS styling
✅ Complete README documentation

---

**Ready to run!** Execute `composer install` then `php artisan serve`.
