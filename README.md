# Database Converter for Laravel Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/richan-fongdasen/database-converter-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/database-converter-laravel)
[![License: MIT](https://poser.pugx.org/richan-fongdasen/database-converter-laravel/license.svg)](https://opensource.org/licenses/MIT)
[![PHPStan](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/phpstan.yml)
[![Test](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/test.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/test.yml)
[![Coding Style](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/coding-style.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/database-converter-laravel/actions/workflows/coding-style.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/richan-fongdasen/database-converter-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/database-converter-laravel)

This package provides a simple way to convert your database schema from one database management system to another. It is built on top of the [Laravel](https://laravel.com) framework.

## Installation

You can install the package via composer:

```bash
composer require richan-fongdasen/database-converter-laravel
```

### Publishing Configuration

You can publish the configuration file using the following command:

```bash
php artisan vendor:publish --provider="RichanFongdasen\DatabaseConverter\DatabaseConverterServiceProvider"
```

The above command publishes the configuration file to `config/database-converter-laravel.php`, and the content of the configuration file will look like this:

```php
return [
    'chunk_size' => 700,
    
    /*
     * Tables to ignore during conversion.
     * By default, the 'migrations' table is always ignored.
     */
    'ignore_tables' => [
        // 'table_name_to_ignore',
        // 'another_table_to_ignore',
    ],
];
```

### Configuration Options

- **chunk_size**: The number of records to process in each batch during data conversion. Default is 700.
- **ignore_tables**: An array of table names to ignore during the conversion process. The `migrations` table is always ignored regardless of this configuration.

## Usage

In this example, we will convert the database schema from MySQL to SQLite.

### Configure the database connection as conversion target

First, you need to configure the database connection that you want to convert to. You can do this by adding a new database connection configuration in your `config/database.php` file.

```php
'connections' => [
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => database_path('database.sqlite'),
        'prefix' => '',
        'foreign_key_constraints' => true,
    ],
],
```

### Run database migration on the conversion target

Before you can convert the database schema, you need to run the migration on the conversion target database. You can do this by running the following command:

```bash
php artisan migrate --database=sqlite --path=database/migrations
```

### Convert the database schema

Now, you can convert the database schema from MySQL to SQLite by running the following command:

```bash
php artisan db:convert mysql sqlite
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Richan Fongdasen](https://github.com/richan-fongdasen)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
