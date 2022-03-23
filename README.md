# Morningtrain\WP\Eloquent

An implementation of Laravel Eloquent for `Mornintrain\WP\Core`

## What is the "Eloquent"?

This package, implements Laravel Eloquent, and is used to handle custom database tables and models.

You can read more about Laravel Database here: https://laravel.com/docs/9.x/database  
You can read more about Laravel Eloquent here: https://laravel.com/docs/9.x/eloquent

## Getting started

To get started with the module simply construct an instance of `\Morningtrain\WP\Eloquent\Module()` and pass it to the `addModule()` method on your project instance.

### Example

```php
// functions.php
require __DIR__ . "/vendor/autoload.php";

use Morningtrain\WP\Core\Theme;

Theme::init();

// Add our module
Theme::getInstance()->addModule(new \Morningtrain\WP\Eloquent\Module());
```

## Models
The model creation works just like it does in Laravel, but you can not use the scaffolding commands.

The file must be placed inside a `Model` folder in the project root:

### Example
```php
// Models/Car.php
use Illuminate\Database\Eloquent\Model;

class Car extends Model {

}
```

## Database Schma creation
Databases can be created with the Schema facade just like in Laravel. But migrations is not yet implentet, so you need to handle these yourself.

The module will automatically handle and prefix the table (example: 'wp_')

See: https://laravel.com/docs/9.x/migrations#tables

### Example
```php
use \Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::create('cars', function(Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('color');
    $table->dateTime('created_at');
    $table->dateTime('updated_at');
});
```