# Morningtrain\WP\Database

A Morningtrain package that implements Laravel Eloquent and Migrations into WordPress.

## Table of Contents

- [Introduction](#introduction)
- [Getting Started](#getting-started)
    - [Installation](#installation)
- [Dependencies](#dependencies)
    - [illuminate/database](#illuminatedatabase)
- [Usage](#usage)
  - [Initializing package](#initializing-package)
  - [Creating a Model](#creating-a-model)
  - [Creating a Migration](#creating-a-migration)
  - [Running migrations](#running-migrations)
- [Credits](#credits)
- [Testing](#testing)
- [License](#license)

## Introduction

## Getting Started

To get started install the package as described below in [Installation](#installation).

To use the tool have a look at [Usage](#usage)

### Installation

Install with composer

```bash
composer require morningtrain/wp-database
```

## Dependencies

### illuminate/database

[Database](https://laravel.com/docs/database)

## Usage

### Initializing package

```php
<?php
    Database::setup(__DIR__ . "/database/migrations");
```

### Creating a Model

In `app/Models`

```php
// Foo.php
<?php

    namespace MyProject\App\Models;

    /**
     * @property int $id
     * @property string $title
     */
    class Foo extends \Illuminate\Database\Eloquent\Model
    {
        public $timestamps = false;
        protected $table = 'foo';
    }
```

### Creating a Migration

```shell
wp make:migration create_foo_table
```

Will create a new migration file for you with `Schema::create('foo')` already prepared.

### Running migrations

You can run all new migrations like so:

Using `wp cli`:

```shell
wp dbmigrate
```

Using `php`:

```php
<?php
    \Morningtrain\WP\Database\Database::migrate();
?>
```

## Credits

- [Mathias Munk](https://github.com/mrmoeg)
- Martin Schadegg Brønniche
- [All Contributors](../../contributors)

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
