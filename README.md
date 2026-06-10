# 📊 Commodity Price Monitoring Dashboard

A web-based dashboard for monitoring and tracking basic food commodity prices. Built with **Laravel 13**, **Blade**, **Tailwind CSS v4**, and **SQLite** — designed for clean architecture, maintainability, and future migration to MySQL/MongoDB.

## ✨ Features

- **Dashboard Overview** — Summary cards, trend indicators, latest prices, and Chart.js visualizations (price trend line chart + region comparison bar chart)
- **Commodity Management** — CRUD for commodities (e.g., Beras, Minyak Goreng, Gula Pasir)
- **Region Management** — CRUD for regions with hierarchical support (province/city/district)
- **Price Records** — Record daily prices per commodity per region with filtering by date, commodity, and region
- **Price Predictions** — Generate future price forecasts using SMA-7 + Linear Regression with confidence scoring (7/14/30-day periods)
- **Automated Weekly Predictions** — Scheduled weekly generation of 7-day forecasts for all commodity+region pairs
- **AI Market Insights** — Natural-language trend summaries powered by Groq (OpenAI-compatible API)
- **Prediction Batch Tracking** — Full lifecycle tracking with status monitoring (pending/processing/completed/failed)
- **Authentication** — Simple session-based login/logout
- **Responsive UI** — Tailwind CSS v4 with mobile-friendly layout
- **Future-Ready** — Clean Architecture + Repository pattern enables swapping SQLite for MySQL/MongoDB

## 🧱 Architecture

This project follows **Clean Architecture** with four layers:

```
┌──────────────────────────────────────┐
│         PRESENTATION LAYER           │
│  Controllers · Blade Views · Blocs   │
│  ViewModels · Components (Tailwind)  │
├──────────────────────────────────────┤
│         APPLICATION LAYER            │
│  Use Cases · DTOs · Services         │
├──────────────────────────────────────┤
│           DOMAIN LAYER               │
│  Entities · Value Objects            │
│  Repository Contracts                │
├──────────────────────────────────────┤
│        INFRASTRUCTURE LAYER          │
│  Eloquent Models · Repositories      │
│  Migrations · External Integrations  │
└──────────────────────────────────────┘
```

### BLoC-like State Pattern

For presentation state management, a **BLoC-like** pattern is used:

```
Controller (thin)
  → Bloc (receives request, coordinates)
    → UseCase (application logic)
      → Repository (interface)
        → EloquentRepository (infrastructure)
          → returns data
  → Bloc produces ViewModel
    → passes ViewModel to Blade view
      → view only reads properties, calls no services
```

## ⚙️ Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 13 (PHP 8.3+) |
| Frontend | Blade + Tailwind CSS v4 |
| Database | SQLite (current), MySQL & MongoDB (target) |
| Build | Vite + @tailwindcss/vite |
| Testing | PHPUnit 12 |
| Code Style | PSR-12 via PHP_CodeSniffer & Laravel Pint |

## 📁 Project Structure

```
app/
├── Domain/               # Business entities & repository interfaces
│   ├── Entities/
│   ├── ValueObjects/
│   └── Repositories/
├── Application/          # Use cases, DTOs, application services
│   ├── DTOs/
│   ├── UseCases/
│   └── Services/
├── Infrastructure/       # Eloquent implementations
│   └── Repositories/
├── Presentation/         # BLoC classes & ViewModels
│   ├── Blocs/
│   └── ViewModels/
├── Models/               # Eloquent ORM models
├── Http/
│   ├── Controllers/
│   └── Requests/
└── Providers/
database/
├── migrations/
└── seeders/
resources/views/
├── layouts/
├── components/
├── dashboard/
├── commodities/
├── regions/
├── prices/
└── auth/
```

## 🚀 Installation

### Prerequisites

- PHP 8.3+
- Composer
- Node.js & npm
- SQLite (enabled in PHP)

### Setup

```bash
# Clone the repository
git clone <repo-url> price-monitoring
cd price-monitoring

# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database (SQLite)
touch database/database.sqlite
php artisan migrate --seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Default Login

| Email | Password |
|-------|----------|
| `admin@example.com` | `password` |

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage (if Xdebug/PCOV installed)
php artisan test --coverage
```

### Code Style

```bash
# Check PSR-12 compliance
vendor/bin/phpcs --standard=PSR12 --extensions=php app/

# Auto-fix issues
vendor/bin/phpcbf --standard=PSR12 --extensions=php app/

# Or use Laravel Pint
./vendor/bin/pint
```

## 🗄️ Database Schema

### Current (SQLite)

| Table | Purpose |
|-------|---------|
| `commodities` | Basic food commodities (name, category, unit) |
| `regions` | Geographic regions with hierarchy (province/city) |
| `price_records` | Daily price observations per commodity per region |
| `predictions` | Future price predictions (extensible) |
| `prediction_batches` | Weekly prediction runs with status, AI insight, and batch metadata |

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `GROQ_API_KEY` | Groq API key for AI-powered market insights | — |
| `GROQ_MODEL` | LLM model for insight generation | `llama-3.3-70b-versatile` |
| `GROQ_ENDPOINT` | Groq API base URL | `https://api.groq.com/openai/v1` |
| `GROQ_TIMEOUT` | API request timeout in seconds | `30` |

### Future Migration

- **MySQL**: Same relational schema with InnoDB + foreign keys
- **MongoDB**: Price records as documents with embedded commodity/region data; repository interfaces abstract the storage layer

## 📡 API Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/dashboard` | Dashboard overview |
| GET/POST | `/commodities` | List / Create commodities |
| PUT/DELETE | `/commodities/{id}` | Update / Delete commodity |
| GET/POST | `/regions` | List / Create regions |
| GET/POST | `/price-records` | List / Create price records |
| GET | `/predictions` | List predictions |
| GET | `/predictions/create` | Prediction form |
| POST | `/predictions/generate` | Generate predictions (manual) |
| DELETE | `/predictions/{id}` | Delete prediction |
| Artisan | `predictions:generate-weekly` | Generate weekly predictions (scheduled every Monday 02:00) |
| GET/POST | `/login` | Login form / authenticate |
| POST | `/logout` | Logout |

## 🔒 Security

- CSRF protection enabled
- XSS prevented via Blade `{{ }}` escaping
- Authentication required for all data routes
- Input validation via Form Request classes
- SQL injection prevented by Eloquent ORM

## 📋 Roadmap

- [x] Clean Architecture layering
- [x] BLoC-like state management
- [x] SQLite integration
- [x] Authentication
- [x] CRUD commodities, regions, prices
- [x] Dashboard with summaries & trends
- [x] Real chart visualizations (Chart.js)
- [x] Price prediction engine (SMA-7 + Linear Regression)
- [x] Automated weekly predictions with batch tracking
- [x] AI-powered market insights (Groq integration)
- [ ] CSV/Excel import/export
- [ ] MySQL production migration
- [ ] MongoDB adapter implementation
- [ ] API endpoints for external integrations

## 🤝 Contributing

This is a student thesis project. Contributions, issues, and feature requests are welcome.

## 📄 License

[MIT](LICENSE)
