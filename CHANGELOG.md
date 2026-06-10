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

## [1.1.0] — 2026-06-09

### Added

#### 📈 Charts & Visualization
- **Chart.js integration**: Price trend line chart (30-day average) and region comparison bar chart on the dashboard
- **Chart.js bundled** via npm (`chart.js/auto`) and exposed on `window.Chart` for Blade views

#### 🔮 Price Predictions
- **Prediction engine**: Simple Moving Average (SMA-7) + Linear Regression forecasting algorithm
- **Confidence scoring**: Standard deviation-based confidence metric (0–100%) displayed per prediction
- **Prediction CRUD**: Generate, list, and delete predictions with full form validation
- **Configurable periods**: Users can generate 7, 14, or 30-day forecasts for any commodity+region pair
- **Old prediction cleanup**: Regenerating predictions automatically replaces previous forecasts for the same commodity+region
- **Database transaction safety**: Delete + create cycle wrapped in `DB::transaction()` to prevent data loss on failure

#### 🏷️ Display Improvements
- **Commodity/region names**: Tables now show friendly names instead of raw numeric IDs (lookup maps built at Bloc/Controller layer)
- **Parent region name resolution**: Region index resolves parent IDs to region names

### Fixed

- **Duplicate flash messages**: Predictions index no longer renders its own flash blocks (layout already handles them)
- **Confidence precision**: Aligned `round($confidence, 2)` with DB `decimal(5,2)` schema to prevent silent truncation
- **Delete return value**: `PredictionController::destroy()` now checks the boolean return and shows error on missing ID
- **Sample standard deviation**: Changed from population (`/n`) to sample (`/n-1`) formula for statistical correctness
- **PSR-12 spacing**: Fixed string concatenation spacing in `GetDashboardDataUseCase`
- **Type safety**: `getLastRecordDate()` now has a proper `Collection` parameter type hint

### Security

- **Database transaction**: Prediction regeneration is now atomic — old predictions are only deleted after new ones are successfully created

---

## [1.2.0] — 2026-06-10

### Added

#### 🤖 Automated Weekly Predictions
- **Artisan command** `predictions:generate-weekly` — generates 7-day forecasts for all commodity+region pairs (96 pairs, ~672 predictions) with cache-lock overlap protection
- **Laravel scheduler** integration — runs automatically every Monday at 02:00 via `Schedule::command()->weekly()->mondays()->at('02:00')`
- **Duplicate run prevention** — checks for existing completed batch in the last 7 days before creating a new one
- **Manual trigger support** — command can still be invoked manually via `php artisan predictions:generate-weekly` or from the UI

#### 🗂️ Prediction Batch Tracking
- **`prediction_batches` table** — groups all predictions from a single weekly run with full lifecycle tracking
- **Batch statuses**: `pending` → `processing` → `completed` / `failed` / `completed_with_insight`
- **Progress counters**: `total_pairs`, `processed_pairs`, `total_predictions` for visibility into long-running batches
- **Predictions linked to batches**: New `prediction_batch_id` FK on `predictions` (nullable, backward-compatible)

#### 🧠 AI-Powered Market Insights (Groq)
- **`AiInsightService`** — generates natural-language market trend summaries via Groq (OpenAI-compatible API)
- **`GenerateAiInsightJob`** — queued job with 2 retry attempts, gracefully degrades if API is unreachable
- **Configurable** via `GROQ_API_KEY`, `GROQ_MODEL`, `GROQ_ENDPOINT`, `GROQ_TIMEOUT` in `.env`
- **Prompt in Bahasa Indonesia** — outputs plain-text summaries of price trends, top movers, and confidence levels

#### ⚙️ Queue & Job Infrastructure
- **`ProcessWeeklyPredictionsJob`** — processes all pairs in a single queued job with lightweight DB increment tracking
- **`GenerateAiInsightJob`** — separate job for AI insight generation, decoupled from prediction math
- **Database queue driver** — sufficient for weekly scale (~672 rows), uses existing `jobs` table
- **`ShouldBeUnique`** — prevents duplicate job dispatching with 1-hour lock window

#### 🖥️ UI Enhancements
- **Batch status card** — displayed at top of predictions index showing batch ID, timestamps, status badge, and AI insight
- **AI insight panel** — styled indigo card with safe-escaped text and generation timestamp
- **Status badge component** `x-prediction-batch-status` — color-coded badges for all 5 statuses with Indonesian labels and spinner animation for processing state
- **Empty state** — informative message when no batch exists yet, with command hint

### Changed

- **`PredictionService::generatePredictions()`** — now accepts optional `$predictionBatchId` parameter; uses `deleteByCommodityAndRegion()` with `whereNull('prediction_batch_id')` guard to preserve batch predictions
- **`PredictionController::index()`** — loads latest batch and batch-specific predictions for the view
- **`EloquentPredictionRepository`** — added `findByBatchId()`, `deleteByCommodityAndRegion()` with batch-safe guard

### Fixed

- **Manual prediction safety** — manual generate no longer deletes weekly batch predictions (guarded by `whereNull('prediction_batch_id')`)
- **Job retry logic** — `GenerateAiInsightJob` now throws exception instead of calling `$this->fail()`, enabling proper queue retry mechanism
- **Batch status accuracy** — `completed_with_insight` only set when AI insight is successfully generated, otherwise stays `completed`
- **DB write performance** — `ProcessWeeklyPredictionsJob` uses lightweight `increment()` instead of full entity save per pair

### Security

- **AI insight output** — always escaped via Blade `{{ }}` auto-escaping to prevent XSS
- **Batch isolation** — batch predictions are protected from manual deletion via `prediction_batch_id` null guard

---

## [1.3.0] — 2026-06-11

### Added

#### 🎨 UI & Design
- **Landing page redesign**: Golden Ratio / Rule of Thirds layout applied to `welcome.blade.php` — hero section with split composition, proportional spacing, and focal point alignment
- **Dark mode toggle**: Full dark mode support with `Alpine.store('theme')` — toggle persisted in `localStorage`, applied via `class="dark"` on `<html>`, works across all pages

#### 📄 Predictions — Pagination & Filters
- **Pagination on predictions index**: 15 predictions per page with Laravel `paginate()` and `->appends()` for filter persistence
- **Filter by commodity, region, and period**: Dropdown filters preserved across pagination via query string
- **Filter action button**: Explicit "Filter" button (instead of auto-submit) for predictable UX — filters only apply on user action

#### 🤖 Multi-Model AI Fallback
- **Dual-model chain**: Primary `llama-3.3-70b-versatile` → fallback `llama-3.1-8b-instant` on failure, maximizing insight availability
- **Dashboard AI insight panel**: New "Ringkasan Pasar (AI)" card on dashboard with gradient indigo-purple background, Markdown-rendered insight text, and generation timestamp
- **Configurable models**: `GROQ_PRIMARY_MODEL` and `GROQ_FALLBACK_MODEL` env vars, both defaulting to sensible values

#### 🗺️ Regional Data Expansion
- **34 provinces**: `RegionSeeder` expanded from 8 to all 34 Indonesian provinces, covering Aceh to Papua
- **Seeder optimization**: `PriceRecordSeeder` now inserts in chunks of 500 instead of 1 record at a time (~40× faster seeding)

### Changed

- **Dark mode state**: Migrated from `Alpine.data('themeManager')` component-scoped state to `Alpine.store('theme')` — global reactivity eliminates toggle flicker across page navigation
- **Prediction display logic**: Removed dual `$displayPredictions` / `$predictions` paths — table always renders paginated `$predictions`
- **`.env` key alignment**: Renamed `GROQ_BASE_URL` → `GROQ_ENDPOINT` and `GROQ_MODEL` → `GROQ_PRIMARY_MODEL` to match `config/services.php` expectations

### Fixed

- **AI panel not showing** (primary): Cached null insight no longer locks the panel for 1 hour — API failures fall back to stale cache; only successful API responses are cached
- **Predictions pagination broken**: Filter parameters are now appended to pagination links via `->appends(request()->query())`
- **Dark mode flicker**: Global Alpine store eliminates the flash of wrong theme on page load
- **`.env` variable mismatch**: `GROQ_BASE_URL` and `GROQ_MODEL` were silently ignored — config defaults were used instead of env values

---

## [Unreleased]

### Planned

- [ ] CSV/Excel import/export for price records
- [ ] MySQL production-ready configuration
- [ ] MongoDB repository adapter
- [ ] REST API endpoints for external integrations
- [ ] User roles and permissions
- [ ] Email notifications for price alerts

---

## How to Release

```bash
# Update version in config/app.php
# Commit changes
git tag v1.2.0
git push origin v1.2.0
```
