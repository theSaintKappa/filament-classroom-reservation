# Blank Filament App

This is a starter template for building applications with Laravel and Filament. It includes a clean setup with authentication, a sample dashboard, and blank pages to get you started quickly.
    
## Tech Stack

- **PHP** 8.4
- **Laravel** 12
- **Filament** 5
- **Tailwind CSS** 4
- **SQLite** (default)
- **Pest** 4 (testing)

## Installation

```bash
git clone https://github.com/zstio-pt/blank-filament-app.git
cd blank-filament-app
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan boost:install
npm run build
```

## Helpfull commands

```bash
php artisan make:filament-user # Create an admin user for the Filament admin panel
composer run dev # Start the development environment
```

## Running the Application

```bash
composer run dev
```

This starts the Laravel dev server, queue worker, log viewer, and Vite simultaneously.

## Testing

```bash
php artisan test
```

## Code Formatting

```bash
vendor/bin/pint
```
