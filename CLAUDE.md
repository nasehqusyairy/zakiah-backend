# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Laravel 11 (PHP 8.2+) JSON API backend for a multi-tenant point-of-sale / retail system. It serves two clients:

- **Backoffice** (`/api/backoffice/*`): master data (products, locations, brands, promos, roles, employees, loyalties), stock workflows (import, transfer, opname, adjustment), reports and dashboards.
- **Kasir** (cashier app, `/api/kasir/*`): customer orders, payment, sale transactions, refunds, takings (cash-drawer closing), catalogue/stock lookups.

Key packages: `laravel/sanctum` (token auth), `maatwebsite/excel` (product import/export), `barryvdh/laravel-dompdf` (receipt/transfer PDFs in `resources/views/pdf`). The README is the stock Laravel README.

## Commands

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve

php artisan test                                   # all tests
php artisan test --filter=BrandControllerTest      # single class / method
vendor/bin/pint                                    # code style (Laravel Pint)
php artisan route:list --path=kasir                # inspect routes
```

Notes:
- `phpunit.xml` has the sqlite `:memory:` DB override **commented out**, so tests hit whatever DB `.env` points at. Don't add `RefreshDatabase` tests without changing that.
- Existing tests under `tests/` are mostly scaffold placeholders; there is no meaningful test coverage.
- `AppServiceProvider` logs every SQL query at debug level via `DB::listen`, so `storage/logs/laravel.log` grows fast.

## Architecture

### Request context: entity, employee, device
Multi-tenancy is header-driven, not derived from the Sanctum user alone:
- `EntityCheckingMiddleware` reads `x-employee-code`, loads the `Employee` and its `entity`, and **merges them into the request** as `$request->employee` and `$request->entity`.
- `DeviceCheckingMiddleware` (kasir routes only) reads `x-device-code` and merges `$request->device`; failure returns 422 with `relogin: true`.

Controllers scope every query by `$request->entity->id` manually (e.g. `Brand::where('entity_id', ...)`) and check `entity_id` on route-bound models in `show`/`update`. Any new endpoint must do the same.

### Authorization
Laravel Policies in `app/Policies` are unused stubs. Real authorization lives in Form Requests:
- `app/Http/Requests/BaseRequest.php` `authorize()` calls `RoleValidatorService($employee, $page, $action)`, which looks up `$role->entity_permission[$menu][$action]`.
- Each request class sets `protected $page = PageNameConstants::...` and extends `BaseIndexRequest`/`BaseShowRequest` (or sets `$action` from `ActionConstants`).

### Layers
- **Controllers** (`app/Http/Controllers`, plus `Kasir/`, `Report/`, `Dashboard/` subfolders): validate via Form Request, run writes inside `DB::transaction`, return `BaseJsonResponse` (`{message, errors, data}` envelope) or raw paginators. Index endpoints use `paginate($request->limit)` (default `PageConstants::DefaultLimit`); some kasir lists use cursor pagination.
- **Services** (`app/Helpers/Services/<Domain>/`): business logic as small single-purpose classes (`*Creator`, `*Updater`, `*Calculator`, `*ApprovalService`, `*Transformer`). Customer-order pricing (lines, promos, taxes, service charge, payment fees, subtotal) is split across `Services/CustomerOrder`, `Services/Promo`, `Services/Taxes`.
- **Data objects** (`app/Helpers/Data/`): plain PHP DTOs passed between services.
- **Report queries** (`app/Helpers/Queries/`): raw/builder SQL for reports, built on `ReportBaseQuery`/`ReportSaleBaseQuery`.
- **Enums** (`app/Enums`), **constants** (`app/Helpers/Constants`), translations in `lang/en` and `lang/id` (error messages use `__('auth.*')`, `__('entity.*')`, etc.).

### Stock is maintained by model observers
Observers (registered with `#[ObservedBy]` on models) carry important side effects — inspect them before changing stock-related writes:
- Creating a `ProductStockMovement` updates/locks the matching `ProductLocationStock` (stock, min/max/avg/last buy price) and runs `ProductCogsCalculator`. **Stock changes should be made by inserting `ProductStockMovement` rows**, not by editing `ProductLocationStock` directly (`ProductStockMovementBuilder` helps build them).
- `SaleTransactionDetailObserver`, `SaleRefundDetailObserver`, `ProductLocationObserver`, `ProductSellPriceObserver`, `LocationObserver`, `CustomerPointMovementObserver` carry similar cascading side effects; read them before touching those models.

### Kasir checkpoints
`Services/Checkpoint/*Checkpoint` assign unclosed orders/sales (`taking_id` null) at a location to the current device (`checkpoint_device_id`) when a taking is made, so cashier session closing is device-aware.

### Misc
- `database/teja/task/` holds one-off manual data-fix/import scripts; they are not part of the normal migration flow.
- Some PDF routes (`/sale_transactions/{id}/pdf`, `/daily_sales/{id}/pdf`, `/product_transfer_services/{id}/pdf`) are intentionally outside auth middleware.
- `# TEJA` comments mark open TODOs from the original author.
