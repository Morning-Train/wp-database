<?php

    namespace Morningtrain\WP\Database;

    use Morningtrain\WP\Database\Eloquent\Application;
    use Morningtrain\WP\Database\Migration\Migration;

    class Database
    {
        public static function setup(string|array $dir)
        {
            Application::setup();
            Migration::setup((array)$dir);
            static::migrate();
        }

        public static function migrate()
        {
            Migration::migrate();
        }
    }