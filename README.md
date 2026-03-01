# Laravel Project Deployment .htaccess

A Laravel package that automatically places the correct `.htaccess` files into your Laravel project for **Apache / shared hosting deployment**.

When deploying a Laravel application to shared hosting, you typically need:

1. A **root-level** `.htaccess` that redirects all traffic into the `public/` directory.
2. A **public-level** `.htaccess` (inside `public/`) that routes requests through `index.php`.

This package gives you a single Artisan command to drop both files into the right places instantly.

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.0` |
| Laravel | `9.x`, `10.x`, `11.x` |
| Web Server | Apache with `mod_rewrite` enabled |

---

## Installation

Install via Composer:

```bash
composer require jaikumar0101/laravel-project-deployment-htaccess
```

Laravel's **package auto-discovery** will register the service provider automatically. No manual configuration is needed.

---

## Usage

### Place both `.htaccess` files (recommended)

```bash
php artisan htaccess:install
```

This places:
- `.htaccess` → Laravel project root (`/`)
- `public/.htaccess` → `public/` directory

---

### Root `.htaccess` only

```bash
php artisan htaccess:install --root-only
```

---

### Public `.htaccess` only

```bash
php artisan htaccess:install --public-only
```

---

### Overwrite existing files

By default the command will **not** overwrite existing `.htaccess` files. Use `--force` to override:

```bash
php artisan htaccess:install --force
```

---

## What gets placed

### Root `.htaccess`

Located at the **Laravel project root** (same level as `artisan`). It redirects all incoming Apache requests into the `public/` directory, and protects sensitive files from direct access.

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Redirect all requests to the public directory
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Deny access to sensitive files
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "(composer\.json|composer\.lock|package\.json|...)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Public `.htaccess`

Located inside the `public/` directory. This is the standard Laravel `.htaccess` that sends all requests through `index.php` and handles the `Authorization` header.

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_env.c>
        Options -MultiViews -Indexes
        RewriteEngine On

        RewriteCond %{HTTP:Authorization} .
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_URI} (.+)/$
        RewriteRule ^ %1 [L,R=301]

        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </IfModule>
</IfModule>
```

---

## Deployment Checklist (Shared Hosting)

Follow these steps after uploading your Laravel project to shared hosting:

1. **Upload** your entire Laravel project to the server (e.g. via FTP or Git).
2. **Set the document root** (if your host allows it) to point to `public/`. If you cannot change the document root, skip to step 3.
3. **Run the installer** to place the `.htaccess` files:
   ```bash
   php artisan htaccess:install
   ```
4. **Set directory permissions**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
5. **Set environment variables** in your `.env` file, especially:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```
6. **Run migrations** if needed:
   ```bash
   php artisan migrate --force
   ```

---

## Testing

Install dev dependencies and run the test suite:

```bash
composer install
composer test
```

Generate an HTML coverage report:

```bash
composer test-coverage
```

### Test overview

| Suite | File | What it covers |
|---|---|---|
| Unit | `tests/Unit/StubFilesTest.php` | Verifies both stub files exist and contain the expected Apache directives |
| Unit | `tests/Unit/ServiceProviderTest.php` | Verifies the service provider registers the command with all expected options |
| Feature | `tests/Feature/InstallHtaccessCommandTest.php` | End-to-end tests for every flag (`--root-only`, `--public-only`, `--force`) and overwrite behaviour |

---

## Package Structure

```
laravel-project-deployment-htaccess/
├── src/
│   ├── HtaccessServiceProvider.php        # Service provider (auto-discovered)
│   └── Console/
│       └── InstallHtaccessCommand.php     # php artisan htaccess:install
├── stubs/
│   ├── htaccess-root                      # Template for project root .htaccess
│   └── htaccess-public                    # Template for public/ .htaccess
├── composer.json
└── README.md
```

---

## License

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
