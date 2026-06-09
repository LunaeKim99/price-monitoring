# Changelog

All notable changes to the Commodity Price Monitoring Dashboard will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] — 2026-06-09

### Added

#### 🏗️ Architecture
- **Clean Architecture layering**: Domain, Application, Infrastructure, and Presentation layers with strict separation of concerns
- **Repository pattern**: Domain repository interfaces with Eloquent implementations, enabling future swap to MySQL/MongoDB
- **BLoC-like state management**: Dedicated `Bloc` classes (DashboardBloc, PriceFilterBloc) that mediate between controllers and ViewModels
- **Dependency injection**: All services, use cases, repositories, and blocs wired via constructor injection
- **RepositoryServiceProvider**: Centralized DI bindings for all repository interfaces

#### 🗄️ Database
- **SQLite integration**: Fully working SQLite schema with 4 custom migrations
- **Schema migrations**: `commodities`, `regions`, `price_records`, `predictions` tables with proper foreign keys and indexes
- **Seeders**: 12 Indonesian commodities (Beras, Minyak Goreng, Gula, etc.), 8 provinces, ~2880 price records (30 days × 12 commodities × 8 regions)
- **Factory support**: Model factories for commodities, regions, and price records

#### 🎨 Presentation
- **Blade layout**: Master layout with responsive sidebar navigation and top navbar
- **Reusable components**: `card`, `table`, `filter-bar`, `price-badge`, `alert`, `chart-placeholder` Blade components with Tailwind CSS v4
- **Dashboard**: Summary cards (total commodities, regions, records, average price), trend indicators, latest prices table, trending commodities
- **Commodity CRUD**: Create, read, update, and delete commodities with form validation
- **Region management**: List and create regions with type badges (province/city)
- **Price recording**: Record daily prices with commodity, region, price, date, source, and notes fields
- **Price filtering**: Filter by commodity, region, and date range with paginated results
- **Authentication**: Simple session-based login/logout with Remember Me

#### 🧪 Testing
- **Unit tests**: Commodity entity, Money value object, GetDashboardDataUseCase (with mocked repositories)
- **Feature tests**: Dashboard page load, Price Record CRUD operations
- **22 tests total** with 43 assertions, all passing

#### ✅ Quality Assurance
- **PSR-12 compliance**: 100% compliant (verified via PHP_CodeSniffer)
- **Laravel Pint**: Code style fixer configured and ready
- **Error handling**: Form request validation with user-friendly error messages
- **Session flash messages**: Success/error notifications after CRUD operations

### Fixed

- **Trend direction**: Price trend calculation now correctly reports "up" (prices rising) vs "down" (prices falling) by reversing the newest-first data order before comparison
- **N+1 query elimination**: Trending commodities now computed via a single grouped SQL query instead of N sub-queries per commodity
- **Performance**: `getLatest()` method added to repository — fetches only the required N records instead of loading all records into memory
- **Wasteful count**: Dedicated `count()` method on repository instead of `all()->count()` which loaded every row
- **Auth security**: All protected routes now wrapped in `auth` middleware — unauthenticated users cannot access data
- **Tailwind dynamic classes**: Price badge uses mapped class arrays instead of string interpolation (Tailwind v4 JIT-safe)
- **DI correctness**: PriceCalculationService now injected via constructor rather than hard-instantiated
- **DTO safety**: PriceRecordDTO::fromArray() validates required fields and throws descriptive exception on missing data
- **Repository update safety**: `update()` methods throw LogicException if called on entities without an ID
- **Test auth**: Feature tests now authenticate before making requests (compatible with auth middleware)

---

## [Unreleased]

### Planned

- [ ] Chart.js integration for visual price trends
- [ ] Price prediction engine with confidence scoring
- [ ] CSV/Excel import/export for price records
- [ ] MySQL production-ready configuration
- [ ] MongoDB repository adapter
- [ ] REST API endpoints for external integrations
- [ ] User roles and permissions
- [ ] Email notifications for price alerts
- [ ] Dark mode toggle

---

## How to Release

```bash
# Update version in config/app.php
# Commit changes
git tag v1.0.0
git push origin v1.0.0
```
