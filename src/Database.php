<?php

    namespace Morningtrain\WP\Database;

    use Morningtrain\WP\Database\Eloquent\Application;
    use Morningtrain\WP\Database\Migration\Migration;

    class Database
    {
        public static function setup(string $dir)
        {
            Application::setup();
            Migration::setup($dir);
        }

        public static function migrate()
        {
            // TODO: !!
        }
    }