<?php

    namespace Morningtrain\WP\Database;

    use Morningtrain\WP\Database\Cli\Commands;
    use Morningtrain\WP\Database\Eloquent\Application;
    use Morningtrain\WP\Database\Migration\Migration;

    class Database
    {
        private static bool $initialized = false;

        public static function setup(string|array $dir, array $options = [])
        {
            // Backwards compatibility
            if (static::$initialized) {
                if (! empty($dir)) {
                    static::addMigrationPaths($dir);
                }

                if (! empty($options)) {
                    static::addMigrationOptions($options);
                }

                return;
            }

            Application::setup();
            Migration::setup((array) $dir);

            if (defined('WP_CLI')) {
                Commands::register();
            }

            static::$initialized = true;
        }

        public static function addMigrationPaths(string|array $path): void
        {
            Migration::addPaths((array) $path);
        }

        public static function addMigrationOptions(array $options): void
        {
            Migration::addOptions($options);
        }

        public static function migrate()
        {
            Migration::migrate();
        }
    }