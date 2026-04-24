# Salsabila Track - Restaurant Delivery System

A professional Laravel-based delivery tracking system designed for restaurants. Track orders in real-time with a beautiful customer-facing interface and powerful admin dashboard.

## Features

- **Admin Dashboard**: Intuitive interface for managing orders
- **Real-time Tracking**: Customers can track their orders via unique URLs
- **Status Progression**: 4-step workflow (Menunggu → Diproses → Dikirim → Selesai)
- **Duration Tracking**: Automatic calculation of delivery time
- **Late Detection**: Flags orders that exceed 60 minutes
- **Professional Design**: Restaurant-themed UI with elegant styling
- **MySQL Database**: Robust data persistence
- **RESTful API**: JSON endpoints for AJAX operations

## Tech Stack

- **Backend**: PHP 8.1+, Laravel 10
- **Database**: MySQL 8.0+
- **Frontend**: Blade templates, Tailwind CSS, Vanilla JavaScript
- **Fonts**: Playfair Display (headings), Inter (body)

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & npm (optional, for asset compilation)

### Steps

1. **Clone or extract the project**

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=salsabila_delivery
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```
   
   The application will be available at `http://localhost:8000`

7. **Access the application**
   - Admin Dashboard: `http://localhost:8000/admin`
   - Tracking URL format: `http://localhost:8000/tracking/{order-number}`

## Database Schema

### orders Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) | Primary Key |
| order_number | varchar(20) | Unique order ID (e.g., ORD-001) |
| customer_name | varchar(255) | Customer's name |
| order_items | text | List of ordered items |
| status | enum | waiting, processing, shipped, completed |
| order_time | varchar(20) | Time order was placed (HH:mm) |
| started_at | timestamp | When order status changed to processing |
| completed_at | timestamp | When order was completed |
| duration_minutes | int | Total delivery time in minutes |
| is_late | boolean | Whether order exceeded 60 minutes |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin` | Admin dashboard |
| POST | `/admin/orders` | Create new order |
| POST | `/admin/orders/{id}/status/{status}` | Update order status |
| GET | `/admin/orders/json` | Get all orders (JSON) |
| GET | `/tracking/{orderNumber}` | Customer tracking page |

## Order Status Flow

1. **Menunggu** (Waiting) → Default status after order creation
2. **Diproses** (Processing) → Kitchen is preparing the order
3. **Dikirim** (Shipped) → Order is out for delivery
4. **Selesai** (Completed) → Order delivered successfully

## Project Structure

```
salsabilatrack/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php          # Base controller
│   │   │   ├── OrderController.php     # Order management
│   │   │   └── TrackingController.php  # Customer tracking
│   │   └── Kernel.php                  # HTTP kernel
│   ├── Models/
│   │   └── Order.php                   # Order model
│   └── Providers/
│       └── AppServiceProvider.php      # Application service provider
├── config/
│   └── app.php                         # Application configuration
├── database/
│   └── migrations/
│       └── 2024_01_01_000000_create_orders_table.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # Main layout
│       ├── admin/
│       │   └── dashboard.blade.php     # Admin panel
│       └── tracking/
│           ├── index.blade.php         # Tracking page
│           └── not-found.blade.php     # 404 tracking
├── routes/
│   └── web.php                         # Web routes
├── public/
│   ├── css/                            # Stylesheets
│   └── js/                             # JavaScript
├── .env.example                        # Environment template
├── composer.json                       # PHP dependencies
└── artisan                            # Laravel CLI
```

## Design Features

- **Color Palette**:
  - Primary: Orange (#f97316) - Energy & appetite
  - Secondary: Red (#ef4444) - Alerts & urgency
  - Success: Green (#10b981) - Completion
  - Info: Blue (#3b82f6) - Processing state
- **Typography**: Elegant serif for headings, clean sans-serif for body
- **Effects**: Glassmorphism cards, subtle shadows, smooth transitions
- **Responsive**: Mobile-friendly with adaptive layouts

## Customization

### Changing App Name

Edit `config/app.php`:
```php
'name' => env('APP_NAME', 'Your Restaurant Name'),
```

### Modifying Time Threshold

Edit `app/Http/Controllers/OrderController.php` line 62:
```php
$order->is_late = $duration > 60; // Change 60 to desired minutes
```

### Theming Colors

Edit `resources/views/layouts/app.blade.php` in the Tailwind config section.

## Troubleshooting

**Database connection error**: Check MySQL credentials in `.env`

**Migrations already ran**: Run `php artisan migrate:fresh` to reset

**Permission errors**: Ensure `storage/` and `bootstrap/cache/` are writable

**Cache issues**: Run `php artisan config:clear` and `php artisan view:clear`

## License

This project is open-source and available for restaurant use.

## Support

For issues or questions, please check the Laravel documentation first: https://laravel.com/docs

---

**Salsabila Track** - Restaurant Delivery Management System v1.0
