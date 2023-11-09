<?php

    namespace Morningtrain\WP\Database\Cli;

    use Illuminate\Database\Console\Migrations\TableGuesser;
    use Illuminate\Support\Str;
    use Morningtrain\PHPLoader\Loader;
    use Morningtrain\WP\Database\Migration\Migration;
    use Morningtrain\WP\Database\Migration\StubsHandler;

    class Commands
    {

        public static function register()
        {
            if (!class_exists('\Illuminate\Console\View\Components\Info')) {
                Loader::create(__DIR__ . "/view-components");
            }
            \WP_CLI::add_command('make:migration', [static::class, 'make']);
            \WP_CLI::add_command('dbmigrate', [static::class, 'migrate']);
        }

        public static function migrate()
        {
            echo "Running Migrations\n";
            $migrations = [];

            try {
                $migrations = Migration::migrate();
            } catch (\Exception $e) {
                \WP_CLI::error($e->getMessage());
            }

            $count = count($migrations);

            if ($count > 0) {
                foreach ($migrations as $i => $file) {
                    $migrations[$i] = [
                        'file' => $file,
                    ];
                }

                \WP_CLI::success("Ran {$count} migrations successfully");
                \WP_CLI\Utils\format_items('table', $migrations, ['file']);
            } else {
                \WP_CLI::success("No migrations to run");
            }
        }

        public static function make(array $args, array $assocArgs)
        {
            // It's possible for the developer to specify the tables to modify in this
            // schema operation. The developer may also specify if this table needs
            // to be freshly created so we can create the appropriate migrations.
            $migrationName = Str::snake(trim($args[0]));

            echo \WP_CLI::colorize("Make Migration: %g{$migrationName}%n\n\n");

            // We need to show all paths, and the let the user select the path that the migration
            // needs to be made in.

            $paths = Migration::getPaths();
            $pathNumber = 0;

            if (count($paths) > 1) {
                foreach ($paths as $i => $file) {
                    $paths[$i] = [
                        'number' => $i + 1,
                        'file' => $file,
                    ];
                }

                \WP_CLI\Utils\format_items('table', $paths, ['number', 'file']);

                echo \WP_CLI::colorize("Pick a path number: ");

                $pathNumber = null;

                while ($pathNumber === null) {
                    $answer = (int) trim(fgets(STDIN));

                    if (array_key_exists($answer - 1, $paths)) {
                        $pathNumber = $answer - 1;
                        break;
                    }

                    echo \WP_CLI::colorize("%rNumber is not valid. %nPick a valid path number: ");
                }
            }

            $path = \trailingslashit($paths[$pathNumber]['file']);

            echo \WP_CLI::colorize("\nPath selected: %y{$path}%n\n\n");

            if (!is_dir($path)) {
                if (file_exists($path)) {
                    \WP_CLI::error("{$path} exists but is not a directory");
                }
                mkdir($path);
            }

            $dateStr = current_time('Y_m_d_His');
            $fileName = "{$dateStr}_{$migrationName}.php";

            $create = $assocArgs['create'] ?: false;

            $table = $assocArgs['table'] ?? null;

            // If no table was given as an option but a create option is given then we
            // will use the "create" option as the table name. This allows the devs
            // to pass a table name into this option as a short-cut for creating.
            if (empty($table) && is_string($create)) {
                $table = $create;

                $create = true;
            }

            // Next, we will attempt to guess the table name if this the migration has
            // "create" in the name. This will allow us to provide a convenient way
            // of creating migrations that create new tables for the application.
            if (empty($table)) {
                [$table, $create] = TableGuesser::guess($migrationName);
            }

            if (file_exists($path . $fileName)) {
                \WP_CLI::error("Migration File: {$fileName} already exists");
            }

            $stubName = 'migration';

            if (!empty($table)) {
                if ($create) {
                    $stubName = 'migration.create';
                } else {
                    $stubName = 'migration.update';
                }
            }

            echo \WP_CLI::colorize("Generating migration file\n");
            echo \WP_CLI::colorize("in %y{$path}{$fileName}%n\n");

            if (!file_put_contents($path . $fileName, StubsHandler::parseStub($stubName, ['table' => $table]))) {
                \WP_CLI::error("Migration file could not be created: {$fileName}");
            }

            \WP_CLI::success('Migration file was created');
        }
    }